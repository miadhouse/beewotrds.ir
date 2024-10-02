import React, {useState, useEffect} from 'react';
import axios from 'axios';
import Select from 'react-select';
import {useTranslation} from 'react-i18next';
import {FaPlus} from "react-icons/fa";

const FlashcardForm = ({onClose}) => {
    const {t, i18n} = useTranslation();

    const [formData, setFormData] = useState({
        categoryId: '',
        baseLang: '',
        translateLang: '',
        frontWord: '',
        backWord: '',
        level: '',
    });

    const [errors, setErrors] = useState({});
    const [loading, setLoading] = useState(false);
    const [categories, setCategories] = useState([]);
    const [showCategoryPopup, setShowCategoryPopup] = useState(false); // For popup
    const [newCategory, setNewCategory] = useState({ title: '', description: '' }); // New category state
    const [addingCategory, setAddingCategory] = useState(false);

    useEffect(() => {
        // Fetch categories from the API
        const fetchCategories = async () => {
            try {
                const token = localStorage.getItem('token');
                const response = await axios.get('https://beewords.ir/api/categories', {
                    headers: {
                        Authorization: `Bearer ${token}`,
                    },
                });
                console.log('Categories Data:', response.data);

                // Assuming the API returns an array of categories
                if (Array.isArray(response.data.data)) {
                    const categoriesData = response.data.data.map((category) => ({
                        value: category.id,
                        label: category.title,
                    }));
                    setCategories(categoriesData);
                } else {
                    setErrors({general: 'Error fetching categories.'});
                }

            } catch (error) {
                console.error(error);
                setErrors({general: 'Error fetching categories.'});
            }
        };

        fetchCategories();
    }, []);

    const handleChange = (e) => {
        const {name, value} = e.target;
        setFormData({
            ...formData,
            [name]: value,
        });
    };

    const handleCategoryChange = (selectedOption) => {
        setFormData({
            ...formData,
            categoryId: selectedOption ? selectedOption.value : '',
        });
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        setLoading(true);

        try {
            const token = localStorage.getItem('token');
            const response = await axios.post(
                'https://beewords.ir/api/flashcards',
                formData,
                {
                    headers: {
                        'Content-Type': 'application/json',
                        Authorization: `Bearer ${token}`,
                    },
                }
            );

            if (response.data.status === 201) {
                alert('Flashcard successfully created.');
                onClose();
            } else {
                setErrors(response.data.errors || {general: response.data.message});
            }
        } catch (error) {
            console.error(error);
            setErrors({general: 'Error creating flashcard.'});
        } finally {
            setLoading(false);
        }
    };

    // New category popup handling
    const handleNewCategorySubmit = async () => {
        setAddingCategory(true);
        try {
            const token = localStorage.getItem('token');
            const response = await axios.post(
                'https://beewords.ir/api/categories',
                newCategory,
                {
                    headers: {
                        'Content-Type': 'application/json',
                        Authorization: `Bearer ${token}`,
                    },
                }
            );
            if (response.data.status === 201) {
                // Update categories list
                setCategories((prevCategories) => [
                    ...prevCategories,
                    { value: response.data.data.id, label: response.data.data.title }
                ]);
                setShowCategoryPopup(false); // Close popup
            } else {
                setErrors({general: response.data.message});
            }
        } catch (error) {
            console.error(error);
            setErrors({general: 'Error adding category.'});
        } finally {
            setAddingCategory(false);
        }
    };

    return (
        <form onSubmit={handleSubmit}>
            {errors.general && (
                <div className="alert alert-danger">{t(errors.general)}</div>
            )}

            <div className="mb-3">
                <label htmlFor="categoryId" className="form-label w-100">
                    <div className="row w-100">
                        <div className="col-6">
                            {t('Category')}
                        </div>
                        <div className="col-6 text-start p-0">
                            <button
                                type="button"
                                className='btn p-1'
                                onClick={() => setShowCategoryPopup(!showCategoryPopup)}
                            >
                                <FaPlus size={10}/> <small> {t('New category')}  </small>
                            </button>
                        </div>
                    </div>
                </label>
                <Select
                    id="categoryId"
                    name="categoryId"
                    options={categories}
                    onChange={handleCategoryChange}
                    placeholder={t('Select a category...')}
                    isClearable
                />
            </div>

            {showCategoryPopup && (
                <div className="category-popup">
                    <div className="mb-2">
                        <label htmlFor="newCategoryTitle" className="form-label">
                            {t('Title')}
                        </label>
                        <input
                            type="text"
                            className="form-control"
                            id="newCategoryTitle"
                            value={newCategory.title}
                            onChange={(e) => setNewCategory({...newCategory, title: e.target.value})}
                        />
                    </div>
                    <div className="mb-2">
                        <label htmlFor="newCategoryDescription" className="form-label">
                            {t('Description')}
                        </label>
                        <input
                            type="text"
                            className="form-control"
                            id="newCategoryDescription"
                            value={newCategory.description}
                            onChange={(e) => setNewCategory({...newCategory, description: e.target.value})}
                        />
                    </div>
                    <button
                        type="button"
                        className="btn btn-success"
                        onClick={handleNewCategorySubmit}
                        disabled={addingCategory}
                    >
                        {addingCategory ? t('Adding...') : t('Add Category')}
                    </button>
                </div>
            )}

            {/* Other form fields */}
            <div className="mb-3">
                <label htmlFor="baseLang" className="form-label">
                    {t('Base Language')}
                </label>
                <input
                    type="text"
                    className="form-control"
                    id="baseLang"
                    name="baseLang"
                    value={formData.baseLang}
                    onChange={handleChange}
                    required
                />
            </div>
            <div className="mb-3">
                <label htmlFor="translateLang" className="form-label">
                    {t('Translation Language')}
                </label>
                <input
                    type="text"
                    className="form-control"
                    id="translateLang"
                    name="translateLang"
                    value={formData.translateLang}
                    onChange={handleChange}
                    required
                />
            </div>
            <div className="mb-3">
                <label htmlFor="frontWord" className="form-label">
                    {t('Front Word')}
                </label>
                <input
                    type="text"
                    className="form-control"
                    id="frontWord"
                    name="frontWord"
                    value={formData.frontWord}
                    onChange={handleChange}
                    required
                />
            </div>
            <div className="mb-3">
                <label htmlFor="backWord" className="form-label">
                    {t('Back Word')}
                </label>
                <input
                    type="text"
                    className="form-control"
                    id="backWord"
                    name="backWord"
                    value={formData.backWord}
                    onChange={handleChange}
                    required
                />
            </div>
            <div className="mb-3">
                <label htmlFor="level" className="form-label">
                    {t('Level')}
                </label>
                <input
                    type="text"
                    className="form-control"
                    id="level"
                    name="level"
                    value={formData.level}
                    onChange={handleChange}
                    required
                />
            </div>

            <button type="submit" className="btn btn-primary" disabled={loading}>
                {loading ? t('Creating...') : t('Create Flashcard')}
            </button>
        </form>
    );
};

export default FlashcardForm;
