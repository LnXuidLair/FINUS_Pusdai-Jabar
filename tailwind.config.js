import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    safelist: [
        'bg-green-100','text-green-800','ring-green-200',
        'bg-red-100','text-red-800','ring-red-200',
        'bg-blue-100','text-blue-800','ring-blue-200',
        'bg-purple-100','text-purple-800','ring-purple-200',
        'bg-orange-100','text-orange-800','ring-orange-200',
        'bg-gray-100','text-gray-800','ring-gray-200',
        'bg-blue-400',
        'bg-blue-500',
        'bg-indigo-500',
        'bg-indigo-100',
        'bg-green-600',
        'bg-[linear-gradient(to_bottom,_#6B7AD2_0%,_#3340FF_20%,_#1337A2_40%,_#170480_60%,_#3E4DD1_80%,_#688DD6_100%)]',
        'bg-[linear-gradient(to_bottom,_#6BD2C9_0%,_#4DABDE_20%,_#1370A2_40%,_#286355_60%,_#329EB9_80%,_#68A3D6_100%)]',
        'bg-[linear-gradient(to_bottom,_#916FF5_0%,_#6637E8_20%,_#3B099F_40%,_#4F33B1_60%,_#5A33A9_80%,_#8467DB_100%)]',
        'bg-[linear-gradient(to_bottom,_#E2BDF2_0%,_#9B80F9_20%,_#841DB0_40%,_#621085_60%,_#9D71D7_80%,_#DACEF1_100%)]',
        'bg-[linear-gradient(to_bottom,_#F699EA_0%,_#C147D9_20%,_#8E1674_40%,_#711676_60%,_#A81B95_80%,_#ECC1F2_100%)]',
        'bg-[linear-gradient(to_bottom,_#8FF7C3_0%,_#67E896_20%,_#1BA169_40%,_#258765_60%,_#61D098_80%,_#84FF92_100%)]',
        'bg-[linear-gradient(to_right,_#8BEDFF_0%,_#99D8FF_20%,_#8EBBFF_40%,_#A9B7F5_60%,_#BFA8E6_80%,_#C8A2C8_100%)]',
        'bg-[linear-gradient(to_right,_#EA4FFF_0%,_#F153D7_50%,_#F22090_100%)]',
        'bg-[linear-gradient(to_right,_#C8A2C8_0%,_#BFA8E6_20%,_#A9B7F5_40%,_#8EBBFF_60%,_#99D0FF_80%,_#90E0EF_100%)]',
        'bg-[linear-gradient(to_bottom,_#F3E3E3_0%,_#CF30A2_100%)]',
        'bg-[linear-gradient(to_bottom,_#3700FF_0%,_#4F0DB1_100%)]',
        'text-[#DCBCBC]',
        'bg-[linear-gradient(to_bottom,_#6BD2C9_0%,_#70BCE5_20%,_#36AEC8_40%,_#47AEC6_60%,_#329EB9_80%,_#68A3D6_100%)]',
        'bg-[linear-gradient(to_bottom,_#B2181A_0%,_#FF6661_20%,_#FF5255_40%,_#FF7F7F_60%,_#DD0000_80%,_#C9AAAA_100%)]',
        'bg-[linear-gradient(to_bottom,_#C07928_0%,_#F0CF4A_20%,_#F0B85A_40%,_#FFD700_60%,_#F4C430_80%,_#DA8C0E_100%)]',
        'bg-[linear-gradient(to_bottom,_#9B3FC2_0%,_#9B80F9_20%,_#7F1FD8_40%,_#7E4AC1_60%,_#9D6EDB_80%,_#7C3CFA_100%)]',
        'bg-[linear-gradient(to_bottom,_#E6BDFF_4%,_#D483F1_20%,_#CD48FD_40%,_#C171F3_60%,_#DD75F5_80%,_#A912E9_100%)]',
        'bg-[linear-gradient(to_right,_#0FB442_0%,_#1AAF48_39%,_#118635_75%,_#004716_100%)]',
        'bg-[linear-gradient(to_right,_#D9D9D9_0%,_#E5E4E2_25%,_#DFE4EC_50%,_#E5E4E2_75%,_#CBC7C5_100%)]',
        'bg-[linear-gradient(to_right,_#065F22_0%,_#11B745_25%,_#1AE559_50%,_#17AB45_75%,_#077B2B_100%)]',
        // Tambahkan kelas lainnya kalau perlu
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms,
        function ({ addVariant }) {
            addVariant('autofill','&:-webkit-autofill');
        }
    ],
};
