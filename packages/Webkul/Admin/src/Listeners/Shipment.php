<?php

namespace Webkul\Admin\Listeners;

use Webkul\Admin\Mail\Order\InventorySourceNotification;
use Webkul\Admin\Mail\Order\ShippedNotification;
use Webkul\Sales\Contracts\Shipment as ShipmentContract;

class Shipment extends Base
{
    /**
     * After order is created
     *
     * @return void
     */
    public function afterCreated(ShipmentContract $shipment)
    {
        try {
            if (core()->getConfigData('emails.general.notifications.emails.general.notifications.new_shipment_mail_to_admin')) {
                $this->prepareMail($shipment, new ShippedNotification($shipment));
            }

            if (core()->getConfigData('emails.general.notifications.emails.general.notifications.new_inventory_source')) {
                $this->prepareMail($shipment, new InventorySourceNotification($shipment));
            }

            // Update order custom status
            $order = $shipment->order;
            $isCOD = ($order->payment->method === 'cashondelivery' || $order->custom_status === 'เก็บเงินปลายทาง');
            if ($order->custom_status === 'ยืนยันการชำระเงินแล้ว') {
                $newStatus = 'เสร็จสมบูรณ์';
            } else {
                $newStatus = $isCOD ? 'เรียกเก็บเงินจากพนักงานขนส่ง' : 'จัดส่ง';
            }
            $order->update(['custom_status' => $newStatus]);
        } catch (\Exception $e) {
            report($e);
        }
    }
}
