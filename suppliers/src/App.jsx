import React from 'react'
import { HashRouter as Router, Routes, Route } from 'react-router-dom'
import { motion } from 'framer-motion'
import Dashboard from './components/Dashboard'
import SuppliersList from './components/SuppliersList'
import SupplierDetails from './components/SupplierDetails'
import AddSupplier from './components/AddSupplier'
import Navbar from './components/Navbar'
import Sidebar from './components/Sidebar'

function App() {
  return (
    <Router>
      <div className="min-h-screen bg-gray-50">
        <Navbar />
        <div className="flex">
          <Sidebar />
          <main className="flex-1 p-6">
            <motion.div
              initial={{ opacity: 0, y: 20 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ duration: 0.5 }}
            >
              <Routes>
                <Route path="/" element={<Dashboard />} />
                <Route path="/suppliers" element={<SuppliersList />} />
                <Route path="/suppliers/:id" element={<SupplierDetails />} />
                <Route path="/add-supplier" element={<AddSupplier />} />
              </Routes>
            </motion.div>
          </main>
        </div>
      </div>
    </Router>
  )
}

export default App