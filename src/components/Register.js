// src/components/Register.js
import React, { useState, useRef } from 'react';
import axios from 'axios';
import ReCAPTCHA from 'react-google-recaptcha';
import styles from "../assets/landing.module.css";
import { useTranslation } from 'react-i18next';
import { toast } from 'react-toastify'; // Only import toast
import 'react-toastify/dist/ReactToastify.css';
const Register = () => {
    const [userName, setUserName] = useState('');
    const [email, setEmail] = useState('');
    const [password, setPassword] = useState('');
    const [confirmPassword, setConfirmPassword] = useState('');
    const [mobile, setMobile] = useState('');
    const [error, setError] = useState('');
    const [message, setMessage] = useState('');
    const [isLoading, setIsLoading] = useState(false);
    const [recaptchaToken, setRecaptchaToken] = useState(null);
    const recaptchaRef = useRef(null);
    const { t } = useTranslation();

    const handleRegister = async (e) => {
        e.preventDefault();
        setError('');
        setMessage('');
        setIsLoading(true);

        if (password !== confirmPassword) {
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
            const response = await axios.post('https://beewords.ir/api/register', {
                userName,
                email,
                password,
                mobile,
                recaptchaToken,
            });

            const { status } = response.data;

            if (status === 201) {
                toast.success(t('registrationSuccess') || 'Registration successful!');
                // Reset form fields
                setUserName('');
                setEmail('');
                setPassword('');
                setConfirmPassword('');
                setMobile('');
                setRecaptchaToken(null);
                recaptchaRef.current.reset();
            }
        } catch (err) {
            if (err.response) {
                const { status, data } = err.response;

                if (status === 400 && data.errors) {
                    const errorMessages = Object.values(data.errors).join(' ');
                    toast.error(errorMessages);
                } else if (status === 409) {
                    toast.error(data.message || t('emailExists') || 'Email already exists.');
                } else {
                    toast.error(data.message || t('unexpectedError') || 'An unexpected error occurred.');
                }
            } else {
                toast.error(t('serverConnectionError') || 'Could not connect to the server. Please try again.');
            }
        } finally {
            setIsLoading(false);
        }
    };
    const handleRecaptcha = (token) => {
        setRecaptchaToken(token);
    };

    return (
        <div className="register-form-container">
            <form onSubmit={handleRegister}>
                <div className="mb-3">
                    <input
                        type="text"
                        className="form-control authInput"
                        placeholder={t('fullName')}
                        value={userName}
                        onChange={(e) => setUserName(e.target.value)}
                        required
                    />
                </div>
                <div className="mb-3">
                    <input
                        type="email"
                        className="form-control authInput"
                        placeholder={t('emailAddress')}
                        value={email}
                        onChange={(e) => setEmail(e.target.value)}
                        required
                    />
                </div>
                <div className="mb-3">
                    <input
                        type="password"
                        className="form-control authInput"
                        placeholder={t('enterPassword')}
                        value={password}
                        onChange={(e) => setPassword(e.target.value)}
                        required
                    />
                </div>
                <div className="mb-3">
                    <input
                        type="password"
                        className="form-control authInput"
                        placeholder={t('confirmPassword')}
                        value={confirmPassword}
                        onChange={(e) => setConfirmPassword(e.target.value)}
                        required
                    />
                </div>
                <div className="mb-3">
                    <input
                        type="text"
                        className="form-control authInput"
                        placeholder={t('mobileNumber')}
                        value={mobile}
                        onChange={(e) => setMobile(e.target.value)}
                        required
                    />
                </div>
                <div className="mb-3">
                    <ReCAPTCHA
                        ref={recaptchaRef}
                        sitekey="6LdypVMqAAAAALPf5RyL_jufQ08Qt2eEkL8uRemR"
                        onChange={handleRecaptcha}
                        theme="dark"
                    />
                </div>
                {error && <p className="text-danger text-center">{error}</p>}
                {message && <p className="text-success text-center">{message}</p>}
                <button type="submit" disabled={isLoading} className={styles.pushable}>
                    <span className={styles.shadow}></span>
                    <span className={styles.edge}></span>
                    <span className={`${styles.front} authPushBtn`}>
                        {isLoading ? t('registering') : t('register')}
                    </span>
                </button>
            </form>
        </div>
    );
};

export default Register;
