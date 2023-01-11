import React from 'react'

import { Routes, Route } from 'react-router-dom'
import Error404 from '../Error/Error404'
import Home from '../Home/Home'

const Router = () => {
    return (
        <div>

            <Routes>
                <Route path="/" element={<Home />} exact />
                <Route path="/*" element={<Error404 />} exact />
            </Routes>

        </div>
    )
}

export default Router