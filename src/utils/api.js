// src/utils/api.js

import axios from 'axios';

const api = axios.create({
    baseURL: 'https://api.beewords.ir/api', // تنظیم Base URL
    headers: {
        'Content-Type': 'application/json',
    },
});

export default api;
