// src/components/Login.js

import React, { useState, useContext, useRef } from 'react';
import { useNavigate, Link } from 'react-router-dom';
import { AuthContext } from '../context/AuthContext';
import ReCAPTCHA from 'react-google-recaptcha';
import styles from '../assets/landing.module.css';
import { useTranslation } from 'react-i18next';
import { toast } from 'react-toastify';
import 'react-toastify/dist/ReactToastify.css';
import api from '../utils/api'; // Import API instance

const Login = () => {
    const [formData, setFormData] = useState({
        email: '',
        password: '',
    });
    const [recaptchaToken, setRecaptchaToken] = useState(null);
    const [isLoading, setIsLoading] = useState(false);
    const recaptchaRef = useRef(null);
    const navigate = useNavigate();
    const { login } = useContext(AuthContext);
    const { t } = useTranslation();

    const handleLogin = async (e) => {
        e.preventDefault();
        setIsLoading(true);

        if (!recaptchaToken) {
            toast.error(t('completeRecaptcha') || 'Please complete the reCAPTCHA.');
            setIsLoading(false);
            return;
        }

        try {
            const response = await api.post('/login', {
                email: formData.email,
                password: formData.password,
                recaptcha_token: recaptchaToken,
            });

            const { status, token, message } = response.data;

            if (status === true && token) {
                login(token);
                navigate('/');
            } else {
                toast.error(message || t('unexpectedError') || 'An unexpected error occurred.');
            }
        } catch (err) {
            if (err.response) {
                const { status, data } = err.response;
                if (status === 404) {
                    toast.error(data.message || t('userNotFound') || 'User not found.');
                } else if (status === 401) {
                    toast.error(data.message || t('invalidCredentials') || 'Invalid email or password.');
                } else if ((status === 400 || status === 422) && data.errors) {
                    // نمایش خطاهای اعتبارسنجی
                    Object.values(data.errors).forEach((errorMsg) => {
                        toast.error(errorMsg);
                    });
                } else {
                    toast.error(data.message || t('unexpectedError') || 'An unexpected error occurred.');
                }
            } else {
                toast.error(t('serverConnectionError') || 'Could not connect to the server. Please try again.');
            }
        } finally {
            setIsLoading(false);
            if (recaptchaRef.current) {
                try {
                    recaptchaRef.current.reset();
                } catch (error) {
                    console.error('Error resetting ReCAPTCHA:', error);
                }
            }
            setRecaptchaToken(null);
        }
    };

    const handleRecaptcha = (token) => {
        setRecaptchaToken(token);
    };

    const handleChange = (e) => {
        setFormData({ ...formData, [e.target.name]: e.target.value });
    };

    return (
        <div className="login-form-container">
            <form onSubmit={handleLogin}>
                <div className="mb-3">
                    <input
                        type="email"
                        name="email"
                        className="form-control authInput"
                        placeholder={t('emailAddress') || 'Your email address'}
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
                        placeholder={t('enterPassword') || 'Enter password'}
                        value={formData.password}
                        onChange={handleChange}
                        required
                    />
                </div>
                <div className="mb-3">
                    <ReCAPTCHA
                        ref={recaptchaRef}
                        sitekey="6LdypVMqAAAAALPf5RyL_jufQ08Qt2eEkL8uRemR" // کلید تست Google برای محیط توسعه
                        onChange={handleRecaptcha}
                        theme="dark"
                    />
                </div>
                <button type="submit" disabled={isLoading} className={styles.pushable}>
                    <span className={styles.shadow}></span>
                    <span className={styles.edge}></span>
                    <span className={`${styles.front} authPushBtn`}>
                        {isLoading ? t('loggingIn') || 'Logging in...' : t('login') || 'Login'}
                    </span>
                </button>
            </form>
            <div className="auth-links text-center mt-3">
                <Link to="/recover-password" className="recover-password-link">
                    {t('recoverPassword') || 'Recover Password'}
                </Link>
            </div>
        </div>
    );
};

export default Login;
