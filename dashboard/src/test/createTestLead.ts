import type { Lead } from '../features/leads/leadsApi'

export function createTestLead(overrides: Partial<Lead> = {}): Lead {
  return {
    id: 'lead-1',
    full_name: 'Maria Santos',
    company: 'Acme Dental',
    score: 'high',
    estimated_price: 3000,
    created_at: '2026-11-23T10:00:00Z',
    stage: 'new',
    ...overrides,
  }
}
