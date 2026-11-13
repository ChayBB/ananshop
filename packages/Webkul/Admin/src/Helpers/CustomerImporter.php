<?php

namespace Webkul\Admin\Helpers;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Webkul\Core\Rules\PhoneNumber;
use Webkul\Customer\Repositories\CustomerAddressRepository;
use Webkul\Customer\Repositories\CustomerGroupRepository;
use Webkul\Customer\Repositories\CustomerRepository;
use Webkul\User\Repositories\SalesCodeRepository;

/**
 * Reads a spreadsheet produced by the customer export and writes it back.
 *
 * Rows are matched on the customer code, then on the email, so re-importing
 * an exported file updates the same customers instead of duplicating them.
 */
class CustomerImporter
{
    /**
     * Customers created by the current run.
     */
    protected int $created = 0;

    /**
     * Customers updated by the current run.
     */
    protected int $updated = 0;

    /**
     * Rows that could not be imported, keyed by their row number.
     */
    protected array $errors = [];

    /**
     * Customer groups, keyed by their lowercased name.
     */
    protected $groups;

    /**
     * Sales codes, keyed by their code.
     */
    protected $salesCodes;

    /**
     * Countries, keyed by lowercased code and by lowercased name.
     */
    protected array $countries = [];

    /**
     * Create a new helper instance.
     */
    public function __construct(
        protected CustomerRepository $customerRepository,
        protected CustomerAddressRepository $customerAddressRepository,
        protected CustomerGroupRepository $customerGroupRepository,
        protected SalesCodeRepository $salesCodeRepository
    ) {}

    /**
     * Import every row of the uploaded file.
     */
    public function import(UploadedFile $file): array
    {
        $rows = $this->read($file);

        if (empty($rows)) {
            return $this->summary();
        }

        $headings = $this->mapHeadings(array_shift($rows));

        if (! in_array('first_name', $headings, true) && ! in_array('full_name', $headings, true)) {
            $this->errors[0] = [trans('admin::app.customers.customers.index.import.missing-name-column')];

            return $this->summary();
        }

        $this->loadReferenceData();

        foreach ($rows as $index => $row) {
            /**
             * The first row held the headings, so the row number an admin sees
             * in their spreadsheet is two ahead of the index.
             */
            $this->importRow($this->combine($headings, $row), $index + 2);
        }

        return $this->summary();
    }

    /**
     * Read the uploaded file into plain arrays.
     */
    protected function read(UploadedFile $file): array
    {
        $reader = IOFactory::createReaderForFile($file->getRealPath());

        $reader->setReadDataOnly(true);

        $rows = $reader->load($file->getRealPath())
            ->getActiveSheet()
            ->toArray(null, true, false, false);

        return array_values(array_filter(
            $rows,
            fn ($row) => ! empty(array_filter($row, fn ($cell) => trim((string) $cell) !== ''))
        ));
    }

    /**
     * Translate the heading of each column into the field it carries.
     *
     * Both the exported labels and the plain field names are accepted, so a
     * file written by hand works as well as one that came out of the export.
     */
    protected function mapHeadings(array $headings): array
    {
        $labels = [
            trans('admin::app.customers.customers.index.datagrid.customer-code') => 'customer_code',
            trans('admin::app.settings.users.index.create.sales-code') => 'sales_code',
            trans('admin::app.customers.customers.index.create.first-name') => 'first_name',
            trans('admin::app.customers.customers.index.create.last-name') => 'last_name',
            trans('admin::app.customers.customers.index.datagrid.name') => 'full_name',
            trans('admin::app.customers.customers.index.datagrid.email') => 'email',
            trans('admin::app.customers.customers.index.create.line-id') => 'line_id',
            trans('admin::app.customers.customers.index.datagrid.phone') => 'phone',
            trans('admin::app.customers.customers.index.datagrid.status') => 'status',
            trans('admin::app.customers.customers.index.datagrid.gender') => 'gender',
            trans('admin::app.customers.customers.index.create.date-of-birth') => 'date_of_birth',
            trans('admin::app.customers.customers.index.datagrid.group') => 'group',
            trans('admin::app.customers.customers.view.address.create.company-name') => 'address_company_name',
            trans('admin::app.customers.customers.view.address.create.vat-id') => 'address_vat_id',
            trans('admin::app.customers.customers.view.address.create.street-address') => 'address_street',
            trans('admin::app.customers.customers.view.address.create.city') => 'address_city',
            trans('admin::app.customers.customers.view.address.create.state') => 'address_state',
            trans('admin::app.customers.customers.view.address.create.country') => 'address_country',
            trans('admin::app.customers.customers.view.address.create.post-code') => 'address_postcode',
        ];

        $fields = [
            'customer_code',
            'sales_code',
            'first_name',
            'last_name',
            'full_name',
            'email',
            'line_id',
            'phone',
            'status',
            'gender',
            'date_of_birth',
            'group',
            'address_company_name',
            'address_vat_id',
            'address_street',
            'address_city',
            'address_state',
            'address_country',
            'address_postcode',
        ];

        $lookup = [];

        foreach ($labels as $label => $field) {
            $lookup[mb_strtolower(trim($label))] = $field;
        }

        foreach ($fields as $field) {
            $lookup[$field] = $field;
        }

        return array_map(
            fn ($heading) => $lookup[mb_strtolower(trim((string) $heading))] ?? null,
            $headings
        );
    }

    /**
     * Build a row keyed by field name, dropping the columns we do not read.
     */
    protected function combine(array $headings, array $row): array
    {
        $values = [];

        foreach ($headings as $position => $field) {
            if (! $field) {
                continue;
            }

            $value = trim((string) ($row[$position] ?? ''));

            /**
             * A column repeated in the file must not blank out a value that an
             * earlier column already supplied.
             */
            if ($value === '' && isset($values[$field])) {
                continue;
            }

            $values[$field] = $value;
        }

        return $values;
    }

    /**
     * Load the records rows are matched against.
     */
    protected function loadReferenceData(): void
    {
        $this->groups = $this->customerGroupRepository->all(['id', 'name'])
            ->keyBy(fn ($group) => mb_strtolower($group->name));

        $this->salesCodes = $this->salesCodeRepository->all(['id', 'code'])
            ->keyBy(fn ($salesCode) => mb_strtolower($salesCode->code));

        foreach (core()->countries() as $country) {
            $this->countries[mb_strtolower($country->code)] = $country->code;
            $this->countries[mb_strtolower($country->name)] = $country->code;
        }
    }

    /**
     * Import a single row.
     */
    protected function importRow(array $row, int $rowNumber): void
    {
        $row = $this->normalise($row);

        $customer = $this->findCustomer($row);

        if ($messages = $this->validate($row, $customer)) {
            $this->errors[$rowNumber] = $messages;

            return;
        }

        $data = $this->customerData($row);

        if ($customer) {
            $this->customerRepository->update($data, $customer->id);

            $this->updated++;
        } else {
            $customer = $this->customerRepository->create(array_merge($data, [
                'password' => bcrypt(uniqid('', true)),
                'is_verified' => 1,
                'customer_group_id' => $data['customer_group_id'] ?? $this->defaultGroupId(),
                'channel_id' => core()->getCurrentChannel()->id,
            ]));

            $this->created++;
        }

        $this->syncAddress($customer, $row);
    }

    /**
     * Turn the values of a row into what the database stores.
     */
    protected function normalise(array $row): array
    {
        if (empty($row['first_name']) && ! empty($row['full_name'])) {
            $parts = preg_split('/\s+/', $row['full_name'], 2);

            $row['first_name'] = $parts[0] ?? '';

            if (empty($row['last_name'])) {
                $row['last_name'] = $parts[1] ?? '';
            }
        }

        if (! empty($row['gender'])) {
            $gender = ucfirst(mb_strtolower($row['gender']));

            $row['gender'] = in_array($gender, ['Male', 'Female', 'Other'], true) ? $gender : '';
        }

        if (! empty($row['date_of_birth'])) {
            $date = date_create($row['date_of_birth']);

            $row['date_of_birth'] = $date ? $date->format('Y-m-d') : $row['date_of_birth'];
        }

        if (! empty($row['address_country'])) {
            $row['address_country'] = $this->countries[mb_strtolower($row['address_country'])]
                ?? $row['address_country'];
        }

        return $row;
    }

    /**
     * Find the customer a row belongs to, if it is already on file.
     */
    protected function findCustomer(array $row)
    {
        if (! empty($row['customer_code'])) {
            $customer = $this->customerRepository->findOneByField('customer_code', $row['customer_code']);

            if ($customer) {
                return $customer;
            }
        }

        if (! empty($row['email'])) {
            return $this->customerRepository->findOneByField('email', $row['email']);
        }

        return null;
    }

    /**
     * Validate a row, returning the messages that stop it from being imported.
     */
    protected function validate(array $row, $customer): array
    {
        $id = $customer?->id;

        $validator = Validator::make($row, [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'customer_code' => 'nullable|string|max:255|unique:customers,customer_code'.($id ? ','.$id : ''),
            'email' => 'nullable|email|unique:customers,email'.($id ? ','.$id : ''),
            'line_id' => 'nullable|string|max:255|unique:customers,line_id'.($id ? ','.$id : ''),
            'phone' => ['nullable', new PhoneNumber, 'unique:customers,phone'.($id ? ','.$id : '')],
            'gender' => 'nullable|in:Male,Female,Other',
            'date_of_birth' => 'nullable|date|before:today',
            'address_city' => 'nullable|required_with:address_street|string|max:255',
            'address_state' => 'nullable|required_with:address_street|string|max:255',
            'address_country' => 'nullable|required_with:address_street|string|max:255',
            'address_postcode' => 'nullable|required_with:address_street|string|max:255',
        ], $this->validationMessages(), $this->attributeNames());

        $messages = $validator->fails()
            ? collect($validator->errors()->all())->values()->all()
            : [];

        if (! empty($row['sales_code']) && ! $this->salesCodes->has(mb_strtolower($row['sales_code']))) {
            $messages[] = trans('admin::app.customers.customers.index.import.unknown-sales-code', [
                'code' => $row['sales_code'],
            ]);
        }

        if (! empty($row['group']) && ! $this->groups->has(mb_strtolower($row['group']))) {
            $messages[] = trans('admin::app.customers.customers.index.import.unknown-group', [
                'group' => $row['group'],
            ]);
        }

        return $messages;
    }

    /**
     * Messages for the rules a row is checked against.
     *
     * The framework only ships English ones, and an imported row is reported
     * back to the admin, so each rule carries its own wording here.
     */
    protected function validationMessages(): array
    {
        $prefix = 'admin::app.customers.customers.index.import.validation.';

        return [
            'required' => trans($prefix.'required'),
            'required_with' => trans($prefix.'required-with'),
            'email' => trans($prefix.'email'),
            'unique' => trans($prefix.'unique'),
            'date' => trans($prefix.'date'),
            'before' => trans($prefix.'before'),
            'in' => trans($prefix.'invalid'),
            'string' => trans($prefix.'invalid'),
            'max' => trans($prefix.'max'),
        ];
    }

    /**
     * The column heading each field is named by in a message.
     */
    protected function attributeNames(): array
    {
        return [
            'customer_code' => trans('admin::app.customers.customers.index.datagrid.customer-code'),
            'first_name' => trans('admin::app.customers.customers.index.create.first-name'),
            'last_name' => trans('admin::app.customers.customers.index.create.last-name'),
            'email' => trans('admin::app.customers.customers.index.datagrid.email'),
            'line_id' => trans('admin::app.customers.customers.index.create.line-id'),
            'phone' => trans('admin::app.customers.customers.index.datagrid.phone'),
            'gender' => trans('admin::app.customers.customers.index.datagrid.gender'),
            'date_of_birth' => trans('admin::app.customers.customers.index.create.date-of-birth'),
            'address_city' => trans('admin::app.customers.customers.view.address.create.city'),
            'address_state' => trans('admin::app.customers.customers.view.address.create.state'),
            'address_country' => trans('admin::app.customers.customers.view.address.create.country'),
            'address_postcode' => trans('admin::app.customers.customers.view.address.create.post-code'),
        ];
    }

    /**
     * Build the customer columns a row writes.
     */
    protected function customerData(array $row): array
    {
        $data = [];

        /**
         * A column the file leaves out is not touched, while a column that is
         * present but blank clears the value it holds.
         */
        foreach (['first_name', 'last_name'] as $field) {
            if (array_key_exists($field, $row)) {
                $data[$field] = $row[$field];
            }
        }

        foreach (['customer_code', 'email', 'line_id', 'phone', 'gender', 'date_of_birth'] as $field) {
            if (array_key_exists($field, $row)) {
                $data[$field] = $row[$field] !== '' ? $row[$field] : null;
            }
        }

        if (! empty($row['status'])) {
            $data['status'] = in_array(mb_strtolower($row['status']), ['1', 'true', 'yes'], true) ? 1 : 0;
        }

        if (! empty($row['sales_code'])) {
            $data['sales_code_id'] = $this->salesCodes[mb_strtolower($row['sales_code'])]->id;
        }

        if (! empty($row['group'])) {
            $data['customer_group_id'] = $this->groups[mb_strtolower($row['group'])]->id;
        }

        return $data;
    }

    /**
     * Write the address of a row onto the customer's default address.
     */
    protected function syncAddress($customer, array $row): void
    {
        if (empty($row['address_street'])) {
            return;
        }

        $data = [
            'customer_id' => $customer->id,
            'first_name' => $customer->first_name,
            'last_name' => $customer->last_name,
            'email' => $customer->email,
            'phone' => $customer->phone,
            'company_name' => $row['address_company_name'] ?? null,
            'vat_id' => $row['address_vat_id'] ?? null,
            'address' => $row['address_street'],
            'city' => $row['address_city'] ?? null,
            'state' => $row['address_state'] ?? null,
            'country' => $row['address_country'] ?? null,
            'postcode' => $row['address_postcode'] ?? null,
            'default_address' => 1,
        ];

        $address = $this->customerAddressRepository
            ->where('customer_id', $customer->id)
            ->orderByDesc('default_address')
            ->orderBy('id')
            ->first();

        if ($address) {
            $this->customerAddressRepository->update($data, $address->id);
        } else {
            $this->customerAddressRepository->create($data);
        }
    }

    /**
     * The group a new customer falls into when the file does not name one.
     */
    protected function defaultGroupId(): ?int
    {
        return $this->customerGroupRepository->findOneByField('code', 'general')?->id;
    }

    /**
     * The outcome of the run.
     */
    protected function summary(): array
    {
        return [
            'created' => $this->created,
            'updated' => $this->updated,
            'errors' => $this->errors,
        ];
    }
}
