// src/components/VerifyPage.js

import React, { useEffect, useState } from 'react';
import axios from 'axios';
import { useLocation, useNavigate } from 'react-router-dom';
import { toast } from 'react-toastify';

const VerifyPage = () => {
    const [message, setMessage] = useState('');
    const [error, setError] = useState('');
    const [isLoading, setIsLoading] = useState(false);
    const location = useLocation();
    const navigate = useNavigate();

    // استخراج پارامترهای کوئری
    const queryParams = new URLSearchParams(location.search);
    const id = queryParams.get('id');
    const hash = queryParams.get('hash');
    const expires = queryParams.get('expires');
    const signature = queryParams.get('signature');

    useEffect(() => {
        const handleVerify = async () => {
            setError('');
            setMessage('');
            setIsLoading(true);

            if (!id || !hash || !expires || !signature) {
                setError('Invalid verification link.');
                setIsLoading(false);
                return;
            }

            try {
                // ارسال درخواست GET به بک‌اند برای تأیید ایمیل
                const response = await axios.get(`https://api.beewords.ir/api/email/verify/${id}/${hash}`, {
                    params: {
                        expires,
                        signature,
                    },
                });

                if (response.data.status) {
                    setMessage('Email verified successfully! Redirecting to main page...');
                    // هدایت به صفحه اصلی پس از ۲ ثانیه
                    setTimeout(() => {
                        navigate('/');
                    }, 2000);
                } else {
                    setError(response.data.message || 'Email verification failed.');
                }
            } catch (err) {
                setError(err.response?.data?.message || 'Verification failed. Please try again.');
            } finally {
                setIsLoading(false);
            }
        };

        handleVerify();
    }, [id, hash, expires, signature, navigate]);

    return (
        <div className="verify-container">
            <h2>Verify Email</h2>
            {isLoading ? (
                <p>Verifying your email...</p>
            ) : error ? (
                <>
                    <p className="text-danger">{error}</p>
                    <button className="btn btn-secondary" onClick={() => navigate('/auth')} disabled={isLoading}>
                        Go to Auth Page
                    </button>
                </>
            ) : (
                <p className="text-success">{message}</p>
            )}
        </div>
    );
};

export default VerifyPage;

