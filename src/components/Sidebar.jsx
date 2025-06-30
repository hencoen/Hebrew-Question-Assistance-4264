import React from 'react'
import { Link, useLocation } from 'react-router-dom'
import { motion } from 'framer-motion'
import * as FiIcons from 'react-icons/fi'
import SafeIcon from '../common/SafeIcon'

const { FiHome, FiTruck, FiPlus, FiUsers, FiBarChart3 } = FiIcons

const menuItems = [
  { icon: FiHome, label: 'Dashboard', path: '/' },
  { icon: FiTruck, label: 'Suppliers', path: '/suppliers' },
  { icon: FiPlus, label: 'Add Supplier', path: '/add-supplier' },
  { icon: FiUsers, label: 'Contacts', path: '/contacts' },
  { icon: FiBarChart3, label: 'Analytics', path: '/analytics' }
]

function Sidebar() {
  const location = useLocation()

  return (
    <motion.aside
      className="w-64 bg-white shadow-sm border-r border-gray-200 min-h-screen"
      initial={{ x: -250, opacity: 0 }}
      animate={{ x: 0, opacity: 1 }}
      transition={{ duration: 0.3 }}
    >
      <div className="p-6">
        <nav className="space-y-2">
          {menuItems.map((item, index) => {
            const isActive = location.pathname === item.path
            return (
              <motion.div
                key={item.path}
                initial={{ x: -20, opacity: 0 }}
                animate={{ x: 0, opacity: 1 }}
                transition={{ duration: 0.3, delay: index * 0.1 }}
              >
                <Link
                  to={item.path}
                  className={`flex items-center space-x-3 px-4 py-3 rounded-lg transition-all duration-200 ${
                    isActive
                      ? 'bg-blue-50 text-blue-700 border-r-2 border-blue-700'
                      : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'
                  }`}
                >
                  <SafeIcon icon={item.icon} className="text-xl" />
                  <span className="font-medium">{item.label}</span>
                </Link>
              </motion.div>
            )
          })}
        </nav>
      </div>
    </motion.aside>
  )
}

export default Sidebar