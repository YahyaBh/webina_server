import './bootstrap';

import React from 'react';
import ReactDOM from 'react-dom';
import { BrowserRouter } from 'react-router-dom';


import App from './Components/App';


if (document.getElementById('root')) {
    ReactDOM.render(
    <BrowserRouter>
        <App/>
    </BrowserRouter>
        , document.getElementById('root'));
}
