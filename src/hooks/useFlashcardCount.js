// src/hooks/useFlashcardCount.js
import { useState, useEffect, useContext } from 'react';
import api from '../utils/api'; // استفاده از api instance
import { AuthContext } from '../context/AuthContext';

const useFlashcardCount = () => {
    const [count, setCount] = useState(0);
    const [loading, setLoading] = useState(true);
    const { isAuthenticated } = useContext(AuthContext);

    useEffect(() => {
        const fetchCount = async () => {
            if (isAuthenticated) {
                const token = localStorage.getItem('token');
                try {
                    const response = await api.get('/flashcards/count', { // استفاده از مسیر صحیح
                        headers: {
                            Authorization: `Bearer ${token}`,
                        },
                    });
                    if (response.status === 200) {
                        setCount(response.data.count);
                    }
                } catch (error) {
                    console.error('Error fetching flashcard count:', error);
                }
            }
            setLoading(false);
        };

        fetchCount();
    }, [isAuthenticated]);

    return { count, loading };
};

export default useFlashcardCount;
