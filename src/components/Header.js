// src/components/Header.js
import React, { useState } from 'react';
import Sidebar from "./Sidebar";
import ReactFlagsSelect from 'react-flags-select';
import { useTranslation } from 'react-i18next';
import styles from '../assets/Header.module.css'; // Import the CSS Module

const Header = () => {
    const { i18n } = useTranslation();
    const [selected, setSelected] = useState('US'); // Default selected country code

    const handleSelect = (countryCode) => {
        setSelected(countryCode);
        const languageCode = countryCode === 'US' ? 'en' : 'fa';
        i18n.changeLanguage(languageCode);
    };

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

            {/* Language Selector */}
            <div className="language-selector ms-3">
                <ReactFlagsSelect
                    selected={selected}
                    onSelect={handleSelect}
                    countries={["US", "IR", "AS"]}
                    customLabels={{ "US": "", "IR": "" }} // Empty labels
                    placeholder="" // Remove placeholder text
                    showSelectedLabel={false} // Hide selected label
                    showOptionLabel={false} // Hide option labels in dropdown
                    selectedSize={24} // Adjust size as needed
                    optionsSize={24} // Adjust size as needed
                    alignOptionsToRight
                    className="custom-react-flags-select"
                    dropdownClassName="custom-react-flags-dropdown"
                />
            </div>

            <div className="offcanvas offcanvas-start" tabIndex="-1" id="sidebar" aria-labelledby="sidebarLabel">
                <Sidebar />
            </div>
        </div>
    );
};

export default Header;
