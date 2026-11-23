import { describe, expect, it } from 'vitest'
import { createTestLead } from '../../test/createTestLead'
import { applyLeadChange } from './applyLeadChange'

const maria = createTestLead({ id: 'lead-1', full_name: 'Maria Santos' })
const daniel = createTestLead({ id: 'lead-2', full_name: 'Daniel Reyes' })

describe('applyLeadChange', () => {
  it('puts an inserted lead at the top, where the newest leads are', () => {
    const leads = applyLeadChange([maria], { type: 'inserted', lead: daniel })

    expect(leads.map((lead) => lead.id)).toEqual(['lead-2', 'lead-1'])
  })

  it('ignores an insert for a lead that is already on the board', () => {
    const leads = [maria]

    expect(applyLeadChange(leads, { type: 'inserted', lead: maria })).toBe(leads)
  })

  it('replaces an updated lead, such as when the AI score arrives', () => {
    const scoredMaria = { ...maria, score: 'low' as const }

    const leads = applyLeadChange([maria, daniel], { type: 'updated', lead: scoredMaria })

    expect(leads).toEqual([scoredMaria, daniel])
  })

  it('removes a deleted lead', () => {
    const leads = applyLeadChange([maria, daniel], { type: 'deleted', leadId: 'lead-1' })

    expect(leads).toEqual([daniel])
  })

  it('leaves the list alone when an update is for a lead it does not have', () => {
    const leads = applyLeadChange([maria], { type: 'updated', lead: daniel })

    expect(leads).toEqual([maria])
  })
})
