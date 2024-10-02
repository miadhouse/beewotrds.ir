// src/components/Auth.js
import React, { useState, useRef, useEffect } from 'react';
import { Tabs, Tab, Container, Row, Col } from 'react-bootstrap';
import { Navigate } from 'react-router-dom';
import Login from './Login';
import Register from './Register';
import '../assets/AuthPage.css';
import { AuthContext } from '../context/AuthContext';
import Lottie from 'lottie-react';
import splashAnimation from '../assets/splash.json';
import { useTranslation } from 'react-i18next';
import Dropdown from 'react-bootstrap/Dropdown';
import Form from 'react-bootstrap/Form';
import FlagIcon from './FlagIcon'; // ایمپورت FlagIcon برای نمایش پرچم‌ها
import { FaGlobe } from 'react-icons/fa'; // آیکون کره زمین

const Auth = () => {
    const [key, setKey] = useState('login');
    const { isAuthenticated } = React.useContext(AuthContext);
    const lottieRef = useRef(null);
    const { t, i18n } = useTranslation();
    const [selectedCountry, setSelectedCountry] = useState(null);
    const [toggleContents, setToggleContents] = useState(<FaGlobe size={20} />); // آیکون پیش‌فرض کره زمین

    // کشورهایی که زبان دارند
    const countries = [
        { code: 'us', title: 'English' },
        { code: 'ir', title: 'فارسی' },
        { code: 'as', title: 'Other' } // سایر زبان‌ها
    ];

    // تابع برای تغییر زبان
    const handleSelect = (countryCode) => {
        const { code } = countries.find(country => country.code === countryCode);
        setSelectedCountry(code);
        setToggleContents(
            <>
                <FlagIcon code={code} /> {/* نمایش فقط پرچم بعد از انتخاب */}
            </>
        );
        const languageCode = code === 'us' ? 'en' : 'fa';
        i18n.changeLanguage(languageCode);

        // ذخیره زبان انتخاب‌شده در localStorage
        localStorage.setItem('selectedLanguage', code);
    };

    // استفاده از useEffect برای تنظیم زبان اولیه از localStorage
    useEffect(() => {
        const savedLanguage = localStorage.getItem('selectedLanguage');

        if (savedLanguage) {
            // اگر زبانی ذخیره شده باشد، آن را به عنوان زبان انتخاب‌شده تنظیم کنید
            setSelectedCountry(savedLanguage);
            setToggleContents(
                <>
                    <FlagIcon code={savedLanguage} />
                </>
            );

            const languageCode = savedLanguage === 'us' ? 'en' : 'fa';
            i18n.changeLanguage(languageCode);
        } else {
            // اگر زبانی ذخیره نشده باشد، می‌توانید زبان پیش‌فرض را تنظیم کنید
            setToggleContents(<FaGlobe size={20} />);
        }
    }, [i18n]);

    // مدیریت انیمیشن Lottie
    const fps = 30;
    const initialEndFrame = 5.25 * fps;
    const loopStartFrame = initialEndFrame;
    const loopEndFrame = 7 * fps;

    const handleComplete = () => {
        if (lottieRef.current) {
            lottieRef.current.setDirection(1); // Ensure forward direction
            lottieRef.current.playSegments([loopStartFrame, loopEndFrame], true);
        }
    };

    useEffect(() => {
        if (lottieRef.current) {
            // Start by playing the initial segment
            lottieRef.current.playSegments([0, initialEndFrame], true);
        }
    }, []);

    if (isAuthenticated) {
        return <Navigate to="/" />;
    }

    return (
        <div className="auth-container">
            <Lottie
                lottieRef={lottieRef}
                loop={false} // Disable default looping
                autoplay={false} // Control playback manually
                animationData={splashAnimation}
                className="lottie-background"
                onComplete={handleComplete} // Use onComplete callback
            />
            <Container
                fluid
                className="d-flex justify-content-center align-items-center min-vh-100"
            >
                <Row className="w-100 auth-holder">
                    <Col xs={12} sm={8} md={6} lg={4} className="mx-auto">
                        <div className="auth-box p-4 rounded">
                            {/* انتخاب زبان در اینجا */}
                            <div className="language-selector d-flex justify-content-end mb-3">
                                <Form>
                                    <Dropdown onSelect={handleSelect}>
                                        <Dropdown.Toggle
                                            variant="primary"
                                            id="dropdown-flags"
                                            className="text-left d-flex align-items-center"
                                            style={{ width: 50 }} // کاهش عرض برای نمایش فقط پرچم یا آیکون
                                        >
                                            {toggleContents}
                                        </Dropdown.Toggle>

                                        <Dropdown.Menu>
                                            {countries.map(({ code, title }) => (
                                                <Dropdown.Item key={code} eventKey={code}>
                                                    <FlagIcon code={code} /> {title} {/* نمایش پرچم و نام زبان در منو */}
                                                </Dropdown.Item>
                                            ))}
                                        </Dropdown.Menu>
                                    </Dropdown>
                                </Form>
                            </div>

                            <Tabs
                                id="auth-tabs"
                                activeKey={key}
                                onSelect={(k) => setKey(k)}
                                className="mb-3"
                                justify
                            >
                                <Tab className='authNavTanBtn' eventKey="login" title={t('login')}>
                                    <Login />
                                </Tab>
                                <Tab className='authNavTanBtn' eventKey="register" title={t('register')}>
                                    <Register />
                                </Tab>
                            </Tabs>
                        </div>
                    </Col>
                </Row>
            </Container>
        </div>
    );
};

export default Auth;
