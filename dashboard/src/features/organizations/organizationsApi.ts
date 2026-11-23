import { supabase } from '../../lib/supabase'
import type { Tables } from '../../types/database'

export type Organization = Pick<Tables<'organizations'>, 'id' | 'name' | 'slug' | 'is_demo'>

// Row-level security returns only the organizations the signed-in user belongs to.
export async function fetchOrganizations(): Promise<Organization[]> {
  const { data, error } = await supabase
    .from('organizations')
    .select('id, name, slug, is_demo')
    .order('is_demo')
    .order('name')

  if (error) {
    throw error
  }
  return data
}
