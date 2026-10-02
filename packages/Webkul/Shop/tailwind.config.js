/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        "./src/Resources/**/*.blade.php",
        "./src/Resources/**/*.js",

        // Theme overlays in resources/themes are scanned too, so classes used
        // only by an overlay still make it into the compiled stylesheet.
        "../../../resources/themes/**/*.blade.php",
    ],

    theme: {
        container: {
            center: true,

            screens: {
                "2xl": "1440px",
            },

            padding: {
                DEFAULT: "90px",
            },
        },

        screens: {
            sm: "525px",
            md: "768px",
            lg: "1024px",
            xl: "1240px",
            "2xl": "1440px",
            1180: "1180px",
            1060: "1060px",
            991: "991px",
            868: "868px",
        },

        extend: {
            colors: {
                navyBlue: "#060C3B",
                lightOrange: "#F6F2EB",
                darkGreen: '#40994A',
                darkBlue: '#0044F2',
                darkPink: '#F85156',

                // Freshket theme palette.
                freshket: {
                    DEFAULT: '#008065',
                    dark: '#006650',
                    light: '#E7FFF6',
                    sale: '#DB2C2C',
                    canvas: '#F3F5FA',
                },

                // Aztech theme palette.
                aztech: {
                    DEFAULT: '#FF6226',
                    dark: '#F45F18',
                    offer: '#26A37C',
                    sale: '#FF6060',
                    canvas: '#F4F4F4',
                    ink: '#020203',
                },
            },

            fontFamily: {
                poppins: ["Poppins", "sans-serif"],
                dmserif: ["DM Serif Display", "serif"],
            },
        }
    },

    plugins: [],

    safelist: [
        {
            pattern: /icon-/,
        }
    ]
};
