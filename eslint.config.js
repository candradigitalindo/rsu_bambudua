import js from "@eslint/js";

export default [
    {
        ignores: ["node_modules/**", "public/**", "storage/**", "vendor/**"],
    },
    js.configs.recommended,
    {
        files: ["resources/**/*.js"],
        languageOptions: {
            ecmaVersion: "latest",
            sourceType: "module",
        },
    },
];
