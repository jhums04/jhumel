import { QueryClient, QueryClientProvider } from '@tanstack/react-query'
import { render, screen } from '@testing-library/react'
import userEvent from '@testing-library/user-event'
import { MemoryRouter, Route, Routes, useLocation } from 'react-router'
import { describe, expect, it, vi } from 'vitest'
import { createTestLeadDetails } from '../../test/createTestLeadDetails'
import { LeadDrawer } from './LeadDrawer'
import { fetchLead, fetchLeadActivities, type LeadActivity, type LeadDetails } from './leadsApi'

vi.mock('./leadsApi', () => ({
  fetchLead: vi.fn<() => Promise<LeadDetails | null>>(),
  fetchLeadActivities: vi.fn<() => Promise<LeadActivity[]>>(),
}))

function BoardStandIn() {
  const location = useLocation()
  return <p>Board at {`${location.pathname}${location.search}`}</p>
}

function renderDrawer({ lead, activities = [] }: { lead: LeadDetails | null; activities?: LeadActivity[] }) {
  vi.mocked(fetchLead).mockResolvedValue(lead)
  vi.mocked(fetchLeadActivities).mockResolvedValue(activities)

  render(
    <QueryClientProvider client={new QueryClient({ defaultOptions: { queries: { retry: false } } })}>
      <MemoryRouter initialEntries={['/leads/lead-1?organization=demo']}>
        <Routes>
          <Route path="/" element={<BoardStandIn />} />
          <Route path="/leads/:leadId" element={<LeadDrawer />} />
        </Routes>
      </MemoryRouter>
    </QueryClientProvider>,
  )
}

describe('LeadDrawer', () => {
  it('shows the contact details, score reason, reply draft, and activity', async () => {
    renderDrawer({
      lead: createTestLeadDetails(),
      activities: [{ id: 1, type: 'stage_changed', details: { from: 'new', to: 'contacted' }, created_at: '2026-11-23T11:00:00Z' }],
    })

    expect(await screen.findByRole('dialog', { name: 'Maria Santos' })).toBeInTheDocument()
    expect(screen.getByRole('link', { name: 'maria@acme.dev' })).toHaveAttribute('href', 'mailto:maria@acme.dev')
    expect(screen.getByText('Full lead-to-CRM system, n8n automation workflow')).toBeInTheDocument()
    expect(screen.getByText('Clear scope and an urgent timeline.')).toBeInTheDocument()
    expect(screen.getByLabelText('Subject')).toHaveValue('Your lead system quote')
    expect(await screen.findByText('Moved from New to Contacted')).toBeInTheDocument()
    expect(screen.getByText('Lead received from the website')).toBeInTheDocument()
  })

  it('shows the budget for a project brief instead of an estimate', async () => {
    renderDrawer({ lead: createTestLeadDetails({ budget: '3k_to_10k', estimated_price: null }) })

    expect(await screen.findByText('$3k to $10k')).toBeInTheDocument()
    expect(screen.queryByText('Estimate')).not.toBeInTheDocument()
  })

  it('keeps edits to the reply draft', async () => {
    renderDrawer({ lead: createTestLeadDetails() })

    const subject = await screen.findByLabelText('Subject')
    await userEvent.clear(subject)
    await userEvent.type(subject, 'Quick question about your quote')

    expect(subject).toHaveValue('Quick question about your quote')
  })

  it('explains that the draft is coming for a lead that is not scored yet', async () => {
    renderDrawer({ lead: createTestLeadDetails({ score: null, draft_subject: null, draft_body: null }) })

    expect(await screen.findByText('The AI draft appears here once the lead is scored.')).toBeInTheDocument()
    expect(screen.getByText('Unscored')).toBeInTheDocument()
  })

  it('says so when the lead does not exist or is not accessible', async () => {
    renderDrawer({ lead: null })

    expect(
      await screen.findByText('This lead does not exist, or it belongs to an organization you are not in.'),
    ).toBeInTheDocument()
  })

  it('closes back to the same board', async () => {
    renderDrawer({ lead: createTestLeadDetails() })

    await userEvent.click(await screen.findByRole('button', { name: 'Close' }))

    expect(await screen.findByText('Board at /?organization=demo')).toBeInTheDocument()
  })
})
