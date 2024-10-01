import React, { useEffect, useState, useRef } from 'react';

const Navigation = () => {
    const [activeLink, setActiveLink] = useState('home');
    const upperRef = useRef(null);
    const linksRef = useRef({
        settings: null,
        home: null,
        book: null,
    });

    const handleNavClick = (link) => {
        setActiveLink(link);
    };

    // تابع به‌روزرسانی موقعیت upper
    const updateUpperPosition = () => {
        const upper = upperRef.current;
        const activeElement = linksRef.current[activeLink];

        if (activeElement && upper) {
            const parent = activeElement.parentElement;
            const parentRect = parent.getBoundingClientRect();
            const navRect = parent.parentElement.parentElement.getBoundingClientRect();

            // محاسبه موقعیت نسبی
            const left = parentRect.left - navRect.left + parentRect.width / 2;
            upper.style.left = `${left}px`;
            upper.style.transform = 'translateX(-50%)';
        }
    };

    useEffect(() => {
        // به‌روزرسانی موقعیت در ابتدا و هنگام تغییر activeLink
        updateUpperPosition();

        // افزودن رویداد resize
        window.addEventListener('resize', updateUpperPosition);

        // پاکسازی رویداد هنگام unmount
        return () => {
            window.removeEventListener('resize', updateUpperPosition);
        };
    }, [activeLink]);

    return (
        <div className="button-nav">
            <nav className="nav">
                <ul className="nav__list">
                    <li className="nav__item">
                        <a
                            href="#"
                            className={`nav__link ${activeLink === 'settings' ? 'active' : ''}`}
                            onClick={() => handleNavClick('settings')}
                            ref={(el) => (linksRef.current['settings'] = el)}
                        >
                            <i className='bx bx-cog'></i>
                        </a>
                    </li>
                    <li className="nav__item">
                        <a
                            href="#"
                            className={`nav__link ${activeLink === 'home' ? 'active' : ''}`}
                            onClick={() => handleNavClick('home')}
                            ref={(el) => (linksRef.current['home'] = el)}
                        >
                            <i className='bx bx-home-alt-2'></i>
                        </a>
                    </li>
                    <li className="nav__item">
                        <a
                            href="#"
                            className={`nav__link ${activeLink === 'book' ? 'active' : ''}`}
                            onClick={() => handleNavClick('book')}
                            ref={(el) => (linksRef.current['book'] = el)}
                        >
                            <i className='bx bx-book'></i>
                        </a>
                    </li>
                </ul>
                <div className="upper" ref={upperRef}></div>
            </nav>

            {/* SVG Clip Path */}
            <div className="svg">
                <svg viewBox="0 0 202.9 45.5">
                    <clipPath id="menu" clipPathUnits="objectBoundingBox" transform="scale(0.0049285362247413 0.021978021978022)">
                        <path fill="#000" d="M6.7,45.5c5.7,0.1,14.1-0.4,23.3-4c5.7-2.3,9.9-5,18.1-10.5c10.7-7.1,11.8-9.2,20.6-14.3c5-2.9,9.2-5.2,15.2-7
              c7.1-2.1,13.3-2.3,17.6-2.1c4.2-0.2,10.5,0.1,17.6,2.1c6.1,1.8,10.2,4.1,15.2,7c8.8,5,9.9,7.1,20.6,14.3c8.3,5.5,12.4,8.2,18.1,10.5
              c9.2,3.6,17.6,4.2,23.3,4H6.7z"/>
                    </clipPath>
                </svg>
            </div>
        </div>
    );
};

export default Navigation;
