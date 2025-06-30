import { createClient } from '@supabase/supabase-js'

// Project ID will be auto-injected during deployment
const SUPABASE_URL = 'https://<PROJECT-ID>.supabase.co'
const SUPABASE_ANON_KEY = '<ANON_KEY>'

// Create mock client for development
const createMockClient = () => ({
  from: () => ({
    select: () => ({
      eq: () => Promise.resolve({ data: [], error: null }),
      order: () => Promise.resolve({ data: [], error: null }),
      limit: () => Promise.resolve({ data: [], error: null })
    }),
    insert: () => Promise.resolve({ data: null, error: null }),
    update: () => Promise.resolve({ data: null, error: null }),
    delete: () => Promise.resolve({ data: null, error: null })
  })
})

// Check if credentials are configured
const isConfigured = SUPABASE_URL !== 'https://<PROJECT-ID>.supabase.co' && SUPABASE_ANON_KEY !== '<ANON_KEY>'

let supabaseClient

if (isConfigured) {
  supabaseClient = createClient(SUPABASE_URL, SUPABASE_ANON_KEY, {
    auth: {
      persistSession: true,
      autoRefreshToken: true
    }
  })
} else {
  console.warn('Supabase credentials not configured properly - using mock client')
  supabaseClient = createMockClient()
}

export default supabaseClient