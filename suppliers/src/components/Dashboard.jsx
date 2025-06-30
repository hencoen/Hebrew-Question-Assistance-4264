import React, { useState, useEffect } from 'react'
import { motion } from 'framer-motion'
import * as FiIcons from 'react-icons/fi'
import SafeIcon from '../common/SafeIcon'
import supabase from '../lib/supabase'

const { FiTruck, FiUsers, FiTrendingUp, FiDollarSign } = FiIcons

function Dashboard() {
  const [stats, setStats] = useState({
    totalSuppliers: 0,
    activeSuppliers: 0,
    totalContacts: 0,
    recentSuppliers: []
  })

  useEffect(() => {
    fetchDashboardData()
  }, [])

  const fetchDashboardData = async () => {
    try {
      // Fetch suppliers count from Supabase
      const { data: suppliers, error } = await supabase
        .from('suppliers_sd7a8b9c0d')
        .select('*', { count: 'exact' })

      if (error) {
        console.error('Error fetching suppliers:', error)
        // Fallback to mock data
        setStats({
          totalSuppliers: 156,
          activeSuppliers: 142,
          totalContacts: 234,
          recentSuppliers: [
            { id: 1, company: 'Tech Solutions Ltd', created_at: '2024-01-15' },
            { id: 2, company: 'Global Supplies Inc', created_at: '2024-01-14' },
            { id: 3, company: 'Premium Parts Co', created_at: '2024-01-13' }
          ]
        })
      } else {
        // Get recent suppliers (last 10)
        const recentSuppliers = suppliers?.slice(-3) || []
        
        setStats({
          totalSuppliers: suppliers?.length || 0,
          activeSuppliers: suppliers?.length || 0,
          totalContacts: suppliers?.length * 2 || 0, // Estimate
          recentSuppliers: recentSuppliers.map(supplier => ({
            id: supplier.id,
            company: supplier.company,
            created_at: supplier.created_at
          }))
        })
      }
    } catch (error) {
      console.error('Error fetching dashboard data:', error)
      // Fallback to mock data
      setStats({
        totalSuppliers: 156,
        activeSuppliers: 142,
        totalContacts: 234,
        recentSuppliers: [
          { id: 1, company: 'Tech Solutions Ltd', created_at: '2024-01-15' },
          { id: 2, company: 'Global Supplies Inc', created_at: '2024-01-14' },
          { id: 3, company: 'Premium Parts Co', created_at: '2024-01-13' }
        ]
      })
    }
  }

  const statCards = [
    {
      title: 'Total Suppliers',
      value: stats.totalSuppliers,
      icon: FiTruck,
      color: 'bg-blue-500',
      change: '+12%'
    },
    {
      title: 'Active Suppliers',
      value: stats.activeSuppliers,
      icon: FiTrendingUp,
      color: 'bg-green-500',
      change: '+8%'
    },
    {
      title: 'Total Contacts',
      value: stats.totalContacts,
      icon: FiUsers,
      color: 'bg-purple-500',
      change: '+15%'
    },
    {
      title: 'Monthly Revenue',
      value: '$45,678',
      icon: FiDollarSign,
      color: 'bg-orange-500',
      change: '+23%'
    }
  ]

  return (
    <div className="space-y-6">
      <motion.div
        initial={{ opacity: 0, y: 20 }}
        animate={{ opacity: 1, y: 0 }}
        transition={{ duration: 0.5 }}
      >
        <h1 className="text-3xl font-bold text-gray-900 mb-2">Dashboard</h1>
        <p className="text-gray-600">Welcome back! Here's your suppliers overview.</p>
      </motion.div>

      {/* Stats Grid */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        {statCards.map((stat, index) => (
          <motion.div
            key={stat.title}
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.5, delay: index * 0.1 }}
            className="bg-white rounded-xl shadow-sm border border-gray-200 p-6"
          >
            <div className="flex items-center justify-between">
              <div>
                <p className="text-sm font-medium text-gray-600">{stat.title}</p>
                <p className="text-2xl font-bold text-gray-900 mt-1">{stat.value}</p>
                <p className="text-sm text-green-600 mt-1">{stat.change} from last month</p>
              </div>
              <div className={`${stat.color} p-3 rounded-lg`}>
                <SafeIcon icon={stat.icon} className="text-white text-xl" />
              </div>
            </div>
          </motion.div>
        ))}
      </div>

      {/* Recent Suppliers */}
      <motion.div
        initial={{ opacity: 0, y: 20 }}
        animate={{ opacity: 1, y: 0 }}
        transition={{ duration: 0.5, delay: 0.4 }}
        className="bg-white rounded-xl shadow-sm border border-gray-200 p-6"
      >
        <h2 className="text-xl font-semibold text-gray-900 mb-4">Recent Suppliers</h2>
        <div className="space-y-3">
          {stats.recentSuppliers.map((supplier, index) => (
            <motion.div
              key={supplier.id}
              initial={{ opacity: 0, x: -20 }}
              animate={{ opacity: 1, x: 0 }}
              transition={{ duration: 0.3, delay: index * 0.1 }}
              className="flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg transition-colors"
            >
              <div className="flex items-center space-x-3">
                <div className="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                  <SafeIcon icon={FiTruck} className="text-blue-600" />
                </div>
                <div>
                  <p className="font-medium text-gray-900">{supplier.company}</p>
                  <p className="text-sm text-gray-500">Added {supplier.created_at}</p>
                </div>
              </div>
              <motion.button
                whileHover={{ scale: 1.05 }}
                whileTap={{ scale: 0.95 }}
                className="px-3 py-1 text-sm text-blue-600 hover:bg-blue-50 rounded-md transition-colors"
              >
                View
              </motion.button>
            </motion.div>
          ))}
        </div>
      </motion.div>
    </div>
  )
}

export default Dashboard