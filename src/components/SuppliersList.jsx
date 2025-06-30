import React, { useState, useEffect } from 'react'
import { Link } from 'react-router-dom'
import { motion } from 'framer-motion'
import * as FiIcons from 'react-icons/fi'
import SafeIcon from '../common/SafeIcon'
import supabase from '../lib/supabase'

const { FiPlus, FiEdit, FiTrash2, FiEye, FiSearch } = FiIcons

function SuppliersList() {
  const [suppliers, setSuppliers] = useState([])
  const [loading, setLoading] = useState(true)
  const [searchTerm, setSearchTerm] = useState('')

  useEffect(() => {
    fetchSuppliers()
  }, [])

  const fetchSuppliers = async () => {
    try {
      setLoading(true)
      
      // Mock data for now - replace with actual Supabase query
      const mockSuppliers = [
        {
          id: 1,
          company: 'Tech Solutions Ltd',
          email: 'contact@techsolutions.com',
          phone: '+1-555-0123',
          city: 'New York',
          status: 'active',
          created_at: '2024-01-15'
        },
        {
          id: 2,
          company: 'Global Supplies Inc',
          email: 'info@globalsupplies.com',
          phone: '+1-555-0124',
          city: 'Los Angeles',
          status: 'active',
          created_at: '2024-01-14'
        },
        {
          id: 3,
          company: 'Premium Parts Co',
          email: 'sales@premiumparts.com',
          phone: '+1-555-0125',
          city: 'Chicago',
          status: 'inactive',
          created_at: '2024-01-13'
        }
      ]
      
      setSuppliers(mockSuppliers)
    } catch (error) {
      console.error('Error fetching suppliers:', error)
    } finally {
      setLoading(false)
    }
  }

  const filteredSuppliers = suppliers.filter(supplier =>
    supplier.company.toLowerCase().includes(searchTerm.toLowerCase()) ||
    supplier.email.toLowerCase().includes(searchTerm.toLowerCase()) ||
    supplier.city.toLowerCase().includes(searchTerm.toLowerCase())
  )

  const deleteSupplier = async (id) => {
    if (window.confirm('Are you sure you want to delete this supplier?')) {
      try {
        // Mock delete - replace with actual Supabase query
        setSuppliers(suppliers.filter(supplier => supplier.id !== id))
      } catch (error) {
        console.error('Error deleting supplier:', error)
      }
    }
  }

  if (loading) {
    return (
      <div className="flex items-center justify-center h-64">
        <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
      </div>
    )
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
          <h1 className="text-3xl font-bold text-gray-900">Suppliers</h1>
          <p className="text-gray-600 mt-1">Manage your suppliers and their information</p>
        </div>
        <Link
          to="/add-supplier"
          className="inline-flex items-center space-x-2 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors"
        >
          <SafeIcon icon={FiPlus} />
          <span>Add Supplier</span>
        </Link>
      </motion.div>

      {/* Search Bar */}
      <motion.div
        initial={{ opacity: 0, y: 20 }}
        animate={{ opacity: 1, y: 0 }}
        transition={{ duration: 0.5, delay: 0.1 }}
        className="relative"
      >
        <SafeIcon 
          icon={FiSearch} 
          className="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-lg" 
        />
        <input
          type="text"
          placeholder="Search suppliers..."
          value={searchTerm}
          onChange={(e) => setSearchTerm(e.target.value)}
          className="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none"
        />
      </motion.div>

      {/* Suppliers Table */}
      <motion.div
        initial={{ opacity: 0, y: 20 }}
        animate={{ opacity: 1, y: 0 }}
        transition={{ duration: 0.5, delay: 0.2 }}
        className="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden"
      >
        <div className="overflow-x-auto">
          <table className="w-full">
            <thead className="bg-gray-50 border-b border-gray-200">
              <tr>
                <th className="text-left px-6 py-4 font-medium text-gray-900">Company</th>
                <th className="text-left px-6 py-4 font-medium text-gray-900">Email</th>
                <th className="text-left px-6 py-4 font-medium text-gray-900">Phone</th>
                <th className="text-left px-6 py-4 font-medium text-gray-900">City</th>
                <th className="text-left px-6 py-4 font-medium text-gray-900">Status</th>
                <th className="text-left px-6 py-4 font-medium text-gray-900">Actions</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-gray-200">
              {filteredSuppliers.map((supplier, index) => (
                <motion.tr
                  key={supplier.id}
                  initial={{ opacity: 0, y: 20 }}
                  animate={{ opacity: 1, y: 0 }}
                  transition={{ duration: 0.3, delay: index * 0.05 }}
                  className="hover:bg-gray-50 transition-colors"
                >
                  <td className="px-6 py-4">
                    <div className="font-medium text-gray-900">{supplier.company}</div>
                  </td>
                  <td className="px-6 py-4 text-gray-600">{supplier.email}</td>
                  <td className="px-6 py-4 text-gray-600">{supplier.phone}</td>
                  <td className="px-6 py-4 text-gray-600">{supplier.city}</td>
                  <td className="px-6 py-4">
                    <span
                      className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${
                        supplier.status === 'active'
                          ? 'bg-green-100 text-green-800'
                          : 'bg-red-100 text-red-800'
                      }`}
                    >
                      {supplier.status}
                    </span>
                  </td>
                  <td className="px-6 py-4">
                    <div className="flex items-center space-x-2">
                      <Link
                        to={`/suppliers/${supplier.id}`}
                        className="p-2 text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                      >
                        <SafeIcon icon={FiEye} />
                      </Link>
                      <button className="p-2 text-gray-600 hover:text-green-600 hover:bg-green-50 rounded-lg transition-colors">
                        <SafeIcon icon={FiEdit} />
                      </button>
                      <button
                        onClick={() => deleteSupplier(supplier.id)}
                        className="p-2 text-gray-600 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                      >
                        <SafeIcon icon={FiTrash2} />
                      </button>
                    </div>
                  </td>
                </motion.tr>
              ))}
            </tbody>
          </table>
        </div>
      </motion.div>

      {filteredSuppliers.length === 0 && (
        <motion.div
          initial={{ opacity: 0 }}
          animate={{ opacity: 1 }}
          transition={{ duration: 0.5 }}
          className="text-center py-12"
        >
          <SafeIcon icon={FiSearch} className="mx-auto text-6xl text-gray-400 mb-4" />
          <p className="text-xl text-gray-600">No suppliers found</p>
          <p className="text-gray-500 mt-2">Try adjusting your search criteria</p>
        </motion.div>
      )}
    </div>
  )
}

export default SuppliersList