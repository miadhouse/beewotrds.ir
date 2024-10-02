import React from 'react';
import ReactDOM from 'react-dom/client';
import App from './App';
import 'bootstrap/dist/css/bootstrap.min.css';          // Bootstrap CSS
import 'boxicons/css/boxicons.min.css';                 // Boxicons CSS
import 'bootstrap/dist/js/bootstrap.bundle.min.js';     // Bootstrap JS Bundle (includes Popper.js)
import './index.css';                                    // Your custom CSS

const root = ReactDOM.createRoot(document.getElementById('root'));
root.render(<App />);
