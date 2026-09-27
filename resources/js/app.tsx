import './bootstrap';
import Alpine from 'alpinejs';
import React from 'react';
import { createRoot } from 'react-dom/client';
import ProctoringDemo from './components/ProctoringDemo';

// Inisialisasi Alpine.js
(window as any).Alpine = Alpine;
Alpine.start();

// Mount React Proctoring Demo
const container = document.getElementById('proctoring-app');
if (container) {
    const root = createRoot(container);
    root.render(
        <React.StrictMode>
            <ProctoringDemo />
        </React.StrictMode>
    );
}