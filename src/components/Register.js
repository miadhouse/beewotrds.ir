// src/components/Register.js

import React, { useState, useRef } from 'react';
import axios from 'axios';
import ReCAPTCHA from 'react-google-recaptcha';
import styles from '../assets/landing.module.css';
import { useTranslation } from 'react-i18next';
import { toast } from 'react-toastify';
import 'react-toastify/dist/ReactToastify.css';
import { useNavigate } from 'react-router-dom'; // ایمپورت useNavigate
import api from '../utils/api'; // Import API instance

const Register = () => {
    const [formData, setFormData] = useState({
        userName: '',
        email: '',
        password: '',
        confirmPassword: '',
        mobile: '',
    });
    const [isLoading, setIsLoading] = useState(false);
    const [recaptchaToken, setRecaptchaToken] = useState(null);
    const recaptchaRef = useRef();
    const { t } = useTranslation();
    const navigate = useNavigate(); // استفاده از useNavigate

    const handleRegister = async (e) => {
        e.preventDefault();
        setIsLoading(true);

        if (formData.password !== formData.confirmPassword) {
            toast.error(t('passwordMismatch') || 'Passwords do not match.');
            setIsLoading(false);
            return;
        }

        if (!recaptchaToken) {
            toast.error(t('completeRecaptcha') || 'Please complete the reCAPTCHA.');
            setIsLoading(false);
            return;
        }

        try {
            const response = await api.post('/register', {
                name: formData.userName,
                email: formData.email,
                password: formData.password,
                password_confirmation: formData.confirmPassword,
                mobile: formData.mobile,
                recaptcha_token: recaptchaToken,
            });

            if (response.status === 200) {
                toast.success(t('registrationSuccess') || 'Registration successful! Please verify your email.');
                // هدایت به صفحه اصلی
                navigate('/');
                // یا هدایت به صفحه‌ای خاص مانند /verify
                // navigate('/verify');
                // Reset the form
                setFormData({
                    userName: '',
                    email: '',
                    password: '',
                    confirmPassword: '',
                    mobile: '',
                });
            }
        } catch (err) {
            if (err.response) {
                const { status, data } = err.response;

                if (status === 422 && data.errors) {
                    // نمایش هر خطای اعتبارسنجی
                    Object.values(data.errors).forEach((errorMessages) => {
                        errorMessages.forEach((errorMessage) => {
                            toast.error(errorMessage);
                        });
                    });
                } else {
                    toast.error(data.message || t('unexpectedError') || 'An unexpected error occurred.');
                }
            } else {
                toast.error(t('serverConnectionError') || 'Could not connect to the server. Please try again.');
            }
        } finally {
            setIsLoading(false);
            setRecaptchaToken(null);
            if (recaptchaRef.current) {
                recaptchaRef.current.reset();
            }
        }
    };

    const handleRecaptcha = (token) => {
        setRecaptchaToken(token);
    };

    const handleChange = (e) => {
        setFormData({ ...formData, [e.target.name]: e.target.value });
    };

    return (
        <div className="register-form-container">
            <form onSubmit={handleRegister}>
                <div className="mb-3">
                    <input
                        type="text"
                        name="userName"
                        className="form-control authInput"
                        placeholder={t('fullName')}
                        value={formData.userName}
                        onChange={handleChange}
                        required
                    />
                </div>
                <div className="mb-3">
                    <input
                        type="email"
                        name="email"
                        className="form-control authInput"
                        placeholder={t('emailAddress')}
                        value={formData.email}
                        onChange={handleChange}
                        required
                    />
                </div>
                <div className="mb-3">
                    <input
                        type="password"
                        name="password"
                        className="form-control authInput"
                        placeholder={t('enterPassword')}
                        value={formData.password}
                        onChange={handleChange}
                        required
                    />
                </div>
                <div className="mb-3">
                    <input
                        type="password"
                        name="confirmPassword"
                        className="form-control authInput"
                        placeholder={t('confirmPassword')}
                        value={formData.confirmPassword}
                        onChange={handleChange}
                        required
                    />
                </div>
                <div className="mb-3">
                    <input
                        type="text"
                        name="mobile"
                        className="form-control authInput"
                        placeholder={t('mobileNumber')}
                        value={formData.mobile}
                        onChange={handleChange}
                        required
                    />
                </div>
                <input type="hidden" name="language" value="en" />
                <div className="mb-3">
                    <ReCAPTCHA
                        ref={recaptchaRef}
                        sitekey="6LdypVMqAAAAALPf5RyL_jufQ08Qt2eEkL8uRemR"
                        onChange={handleRecaptcha}
                        theme="dark"
                    />
                </div>
                <button type="submit" disabled={isLoading} className={styles.pushable}>
                    <span className={styles.shadow}></span>
                    <span className={styles.edge}></span>
                    <span className={`${styles.front} authPushBtn`}>
                        {isLoading ? t('registering') || 'Registering...' : t('register') || 'Register'}
                    </span>
                </button>
            </form>
        </div>
    );
};

export default Register;
