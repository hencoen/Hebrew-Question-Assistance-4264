import React from 'react'
import { useParams } from 'react-router-dom'
import { motion } from 'framer-motion'
import * as FiIcons from 'react-icons/fi'
import SafeIcon from '../common/SafeIcon'

const { FiMail, FiPhone, FiMapPin, FiCalendar, FiEdit } = FiIcons

function SupplierDetails() {
  const { id } = useParams()
  
  // Mock supplier data - replace with actual data fetching
  const supplier = {
    id: 1,
    company: 'Tech Solutions Ltd',
    email: 'contact@techsolutions.com',
    phone: '+1-555-0123',
    website: 'www.techsolutions.com',
    address: '123 Tech Street, Innovation District',
    city: 'New York',
    state: 'NY',
    zip: '10001',
    country: 'United States',
    status: 'active',
    created_at: '2024-01-15',
    contacts: [
      {
        id: 1,
        name: 'John Smith',
        title: 'Sales Manager',
        email: 'john@techsolutions.com',
        phone: '+1-555-0124'
      }
    ]
  }

  return (
    <div className="space-y-6">
      <motion.div
        initial={{ opacity: 0, y: 20 }}
        animate={{ opacity: 1, y: 0 }}
        transition={{ duration: 0.5 }}
        className="flex items-center justify-between"
      >
        <div>
          <h1 className="text-3xl font-bold text-gray-900">{supplier.company}</h1>
          <p className="text-gray-600 mt-1">Supplier Details</p>
        </div>
        <button className="inline-flex items-center space-x-2 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
          <SafeIcon icon={FiEdit} />
          <span>Edit Supplier</span>
        </button>
      </motion.div>

      <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {/* Main Info */}
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.5, delay: 0.1 }}
          className="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-200 p-6"
        >
          <h2 className="text-xl font-semibold text-gray-900 mb-6">Company Information</h2>
          <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div className="space-y-4">
              <div className="flex items-center space-x-3">
                <SafeIcon icon={FiMail} className="text-gray-400" />
                <div>
                  <p className="text-sm text-gray-500">Email</p>
                  <p className="font-medium text-gray-900">{supplier.email}</p>
                </div>
              </div>
              <div className="flex items-center space-x-3">
                <SafeIcon icon={FiPhone} className="text-gray-400" />
                <div>
                  <p className="text-sm text-gray-500">Phone</p>
                  <p className="font-medium text-gray-900">{supplier.phone}</p>
                </div>
              </div>
              <div className="flex items-center space-x-3">
                <SafeIcon icon={FiCalendar} className="text-gray-400" />
                <div>
                  <p className="text-sm text-gray-500">Created</p>
                  <p className="font-medium text-gray-900">{supplier.created_at}</p>
                </div>
              </div>
            </div>
            <div className="space-y-4">
              <div className="flex items-start space-x-3">
                <SafeIcon icon={FiMapPin} className="text-gray-400 mt-1" />
                <div>
                  <p className="text-sm text-gray-500">Address</p>
                  <p className="font-medium text-gray-900">{supplier.address}</p>
                  <p className="text-gray-600">{supplier.city}, {supplier.state} {supplier.zip}</p>
                  <p className="text-gray-600">{supplier.country}</p>
                </div>
              </div>
            </div>
          </div>
        </motion.div>

        {/* Status Card */}
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.5, delay: 0.2 }}
          className="bg-white rounded-xl shadow-sm border border-gray-200 p-6"
        >
          <h3 className="text-lg font-semibold text-gray-900 mb-4">Status</h3>
          <div className="space-y-4">
            <div>
              <span
                className={`inline-flex items-center px-3 py-1 rounded-full text-sm font-medium ${
                  supplier.status === 'active'
                    ? 'bg-green-100 text-green-800'
                    : 'bg-red-100 text-red-800'
                }`}
              >
                {supplier.status}
              </span>
            </div>
            <div className="pt-4 border-t border-gray-200">
              <p className="text-sm text-gray-500">Website</p>
              <a
                href={`https://${supplier.website}`}
                target="_blank"
                rel="noopener noreferrer"
                className="text-blue-600 hover:text-blue-800 font-medium"
              >
                {supplier.website}
              </a>
            </div>
          </div>
        </motion.div>
      </div>

      {/* Contacts */}
      <motion.div
        initial={{ opacity: 0, y: 20 }}
        animate={{ opacity: 1, y: 0 }}
        transition={{ duration: 0.5, delay: 0.3 }}
        className="bg-white rounded-xl shadow-sm border border-gray-200 p-6"
      >
        <h2 className="text-xl font-semibold text-gray-900 mb-6">Contacts</h2>
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
          {supplier.contacts.map((contact, index) => (
            <motion.div
              key={contact.id}
              initial={{ opacity: 0, scale: 0.9 }}
              animate={{ opacity: 1, scale: 1 }}
              transition={{ duration: 0.3, delay: index * 0.1 }}
              className="border border-gray-200 rounded-lg p-4 hover:border-blue-300 transition-colors"
            >
              <h3 className="font-medium text-gray-900">{contact.name}</h3>
              <p className="text-sm text-gray-500">{contact.title}</p>
              <div className="mt-3 space-y-1">
                <p className="text-sm text-gray-600">{contact.email}</p>
                <p className="text-sm text-gray-600">{contact.phone}</p>
              </div>
            </motion.div>
          ))}
        </div>
      </motion.div>
    </div>
  )
}

export default SupplierDetails