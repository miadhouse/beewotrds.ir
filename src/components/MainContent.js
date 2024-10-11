// src/components/MainContent.js

import React, { useState, useEffect, useContext } from 'react';
import ChartComponent from './ChartComponent';
import SvgGraphic from './SvgGraphic';
import useFlashcardCount from '../hooks/useFlashcardCount';
import useCategoryCount from '../hooks/useCategoryCount';
import styles from '../assets/landing.module.css';
import FlashcardForm from './FlashcardForm';
import { Offcanvas } from 'react-bootstrap';
import { useTranslation } from 'react-i18next';
import { AuthContext } from '../context/AuthContext';
import { toast } from 'react-toastify';

const dataSet = {
    Today: [
        { name: '00:00', view: 10 },
        { name: '01:00', view: 40 },
        { name: '02:00', view: 20 },
        { name: '03:00', view: 10 },
        // ... more data
    ],
    // سایر داده‌ها...
};

const dataSet2 = {
    Today: [
        { name: '00:00', view: 10 },
        { name: '01:00', view: 40 },
        { name: '02:00', view: 20 },
        { name: '03:00', view: 15 },
        // ... more data
    ],
    // سایر داده‌ها...
};

const MainContent = () => {
    const { t, i18n } = useTranslation();
    const { user, isAuthenticated, loading } = useContext(AuthContext);
    const { count: flashcardCount, loading: flashcardLoading } = useFlashcardCount();
    const { count: categoryCount, loading: categoryLoading } = useCategoryCount();

    const [showOffcanvas, setShowOffcanvas] = useState(false);

    const handleNewCardClick = () => {
        setShowOffcanvas(true);
    };

    const handleClose = () => {
        setShowOffcanvas(false);
    };

    useEffect(() => {
        if (user && !user.email_verified_at) {
            console.log('User email not verified'); // لاگ برای بررسی
            toast.warn(t('Your email verification is not completed.'));
        }
    }, [user, t]);

    return (
        <div className="container text-center">
            <div className="center col-12 col-sm-8 col-md-4 col-xl-3 position-relative align-self-center">
                {/* نمایش تعداد فلش‌کارت‌ها */}
                <div className={`widget widget-right text-center`}>
                    {flashcardLoading ? (
                        <h3 className="mb-0">Loading...</h3>
                    ) : (
                        <h3 className="mb-0">{flashcardCount}</h3>
                    )}
                    <small>{t('Flash Card')}</small>
                </div>
                {/* نمایش تعداد دسته‌بندی‌ها */}
                <div className={`widget widget-left text-center`}>
                    {categoryLoading ? (
                        <h3 className="mb-0">Loading...</h3>
                    ) : (
                        <h3 className="mb-0">{categoryCount}</h3>
                    )}
                    <small>{t('Category')}</small>
                </div>

                {/* نمایش نمودارها */}
                <div className={`widget widget-left-bottom text-center`}>
                    <ChartComponent id="myChart" title={t('Last 3 Day')} dataSetKey="Today" dataSet={dataSet} />
                </div>
                <div className={`widget widget-right-bottom text-center`}>
                    <ChartComponent id="myChart2" title={t('Last Month')} dataSetKey="Today" dataSet={dataSet2} />
                </div>
                <SvgGraphic />

                {/* دکمه‌های عملکرد */}
                <div className={styles['button-group']}>
                    <div className="row text-center">
                        <div className="col-6">
                            <button className={styles.pushable} onClick={handleNewCardClick}>
                                <span className={styles.shadow}></span>
                                <span className={styles.edge}></span>
                                <span className={styles.front}> {t('New Card')} </span>
                            </button>
                        </div>
                        <div className="col-6">
                            <button className={styles.pushable}>
                                <span className={styles.shadow}></span>
                                <span className={styles.edge}></span>
                                <span className={styles.front}> {t('All Category')} </span>
                            </button>
                        </div>
                    </div>
                </div>

                {/* Offcanvas برای ساخت فلش‌کارت جدید */}
                <Offcanvas show={showOffcanvas} onHide={handleClose} placement="bottom" container={document.querySelector('.App')}>
                    <Offcanvas.Header closeButton>
                        <Offcanvas.Title>{t('Create a new flashcard')}</Offcanvas.Title>
                    </Offcanvas.Header>
                    <Offcanvas.Body>
                        <FlashcardForm onClose={handleClose} />
                    </Offcanvas.Body>
                </Offcanvas>
            </div>
        </div>
    );
};

export default MainContent;
