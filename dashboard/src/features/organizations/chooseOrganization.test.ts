import { describe, expect, it } from 'vitest'
import { chooseOrganization } from './chooseOrganization'
import type { Organization } from './organizationsApi'

const demo: Organization = { id: 'demo-id', name: 'Demo', slug: 'demo', is_demo: true }
const jhumel: Organization = { id: 'jhumel-id', name: 'Jhumel Builds', slug: 'jhumel', is_demo: false }

describe('chooseOrganization', () => {
  it('uses the organization named in the URL', () => {
    expect(chooseOrganization([jhumel, demo], 'demo')).toBe(demo)
  })

  it('falls back to the real organization when the URL names none', () => {
    expect(chooseOrganization([demo, jhumel], null)).toBe(jhumel)
  })

  it('ignores an organization the user does not belong to', () => {
    expect(chooseOrganization([demo, jhumel], 'someone-else')).toBe(jhumel)
  })

  it('uses the demo when it is the only organization', () => {
    expect(chooseOrganization([demo], null)).toBe(demo)
  })

  it('returns nothing for a user without organizations', () => {
    expect(chooseOrganization([], null)).toBeUndefined()
  })
})
