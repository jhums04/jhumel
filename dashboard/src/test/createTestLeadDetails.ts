import type { LeadDetails } from '../features/leads/leadsApi'
import { createTestLead } from './createTestLead'

export function createTestLeadDetails(overrides: Partial<LeadDetails> = {}): LeadDetails {
  return {
    ...createTestLead(),
    organization_id: 'organization-1',
    email: 'maria@acme.dev',
    phone: '+63 917 000 0000',
    services: ['lead_system', 'automation'],
    timeline: 'rush',
    message: 'We lose quote requests in our inbox.\nWe want them tracked.',
    source: 'website',
    score_reason: 'Clear scope and an urgent timeline.',
    draft_subject: 'Your lead system quote',
    draft_body: 'Hi Maria,\n\nThanks for reaching out.',
    ...overrides,
  }
}
