import { describe, expect, it } from 'vitest'
import { describeActivity } from './describeActivity'
import type { LeadActivity } from './leadsApi'

function activity(type: string, details: LeadActivity['details']): LeadActivity {
  return { id: 1, type, details, created_at: '2026-11-23T10:00:00Z' }
}

describe('describeActivity', () => {
  it('describes a stage change with stage names', () => {
    expect(describeActivity(activity('stage_changed', { from: 'proposal_sent', to: 'won' }))).toBe(
      'Moved from Proposal sent to Won',
    )
  })

  it('falls back when a stage change has unexpected details', () => {
    expect(describeActivity(activity('stage_changed', { from: 'archived' }))).toBe('Stage changed')
  })

  it('describes a sent reply with its subject', () => {
    expect(describeActivity(activity('email_sent', { subject: 'Your quote', delivered: true }))).toBe(
      'Reply sent: Your quote',
    )
  })

  it('says a demo reply was logged but not delivered', () => {
    expect(describeActivity(activity('email_sent', { subject: 'Your quote', delivered: false }))).toBe(
      'Reply logged (demo, not delivered): Your quote',
    )
  })

  it('shows the text of a note', () => {
    expect(describeActivity(activity('note', { text: 'Call back Friday' }))).toBe('Note: Call back Friday')
  })

  it('handles a note without text', () => {
    expect(describeActivity(activity('note', {}))).toBe('Note added')
  })

  it('has a generic sentence for types it does not know', () => {
    expect(describeActivity(activity('imported', []))).toBe('Activity recorded')
  })
})
