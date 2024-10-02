// src/components/Header.js
import React, { useState, useEffect } from 'react';
import Sidebar from "./Sidebar";
import { useTranslation } from 'react-i18next';
import Dropdown from 'react-bootstrap/Dropdown';
import Form from 'react-bootstrap/Form';
import FlagIcon from './FlagIcon'; // کامپوننت FlagIcon خود را ایمپورت کنید
import { FaGlobe } from 'react-icons/fa'; // آیکون کره زمین
import 'bootstrap/dist/css/bootstrap.min.css';

const Header = () => {
    const { i18n } = useTranslation();
    const [selectedCountry, setSelectedCountry] = useState(null);
    const [toggleContents, setToggleContents] = useState(<FaGlobe size={20} />); // آیکون پیش‌فرض کره زمین

    const countries = [
        { code: 'us', title: 'English' },
        { code: 'ir', title: 'فارسی' },
        { code: 'as', title: 'Other' } // سایر زبان‌ها در صورت نیاز
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

    return (
        <div className="header position-absolute z-2 d-flex align-items-center">
            <label className="hamburger" data-bs-toggle="offcanvas" data-bs-target="#sidebar" aria-controls="sidebar">
                <svg viewBox="0 0 32 32" htmlFor="toggle-sidebar">
                    <path className="line line-top-bottom" d="M27 10 13 10C10.8 10 9 8.2 9 6 9 3.5 10.8 2 13 2 15.2 2 17 3.8 17 6L17 26C17 28.2 18.8 30 21 30 23.2 30 25 28.2 25 26 25 23.8 23.2 22 21 22L7 22"></path>
                    <path className="line" d="M7 16 27 16"></path>
                </svg>
            </label>

            <label className="ui-switch ms-3">
                <input type="checkbox" />
                <div className="slider">
                    <div className="circle"></div>
                </div>
            </label>

            {/* انتخاب زبان با استفاده از Dropdown بوت‌استرپ */}
            <div className="language-selector ms-3">
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

            {/* اعمال کلاس شرطی بر اساس زبان انتخاب‌شده */}
            <div className={`offcanvas ${selectedCountry === 'ir' ? 'offcanvas-end' : 'offcanvas-start'}`} tabIndex="-1" id="sidebar" aria-labelledby="sidebarLabel">
                <Sidebar />
            </div>
        </div>
    );
};

export default Header;
