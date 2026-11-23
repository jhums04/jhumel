import { describe, expect, it } from 'vitest'
import { createTestLead } from '../../test/createTestLead'
import { moveLeadToStage } from './moveLeadToStage'

describe('moveLeadToStage', () => {
  const leads = [createTestLead({ id: 'lead-1' }), createTestLead({ id: 'lead-2' })]

  it('changes the stage of the matching lead only', () => {
    const movedLeads = moveLeadToStage(leads, 'lead-2', 'won')

    expect(movedLeads.map((lead) => lead.stage)).toEqual(['new', 'won'])
  })

  it('leaves the original list untouched for rollback', () => {
    moveLeadToStage(leads, 'lead-2', 'won')

    expect(leads.map((lead) => lead.stage)).toEqual(['new', 'new'])
  })
})
