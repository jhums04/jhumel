import { describe, expect, it } from 'vitest'
import { formatCurrency, formatTimeSince } from './format'

describe('formatCurrency', () => {
  it('formats whole dollars with a thousands separator', () => {
    expect(formatCurrency(3000)).toBe('$3,000')
  })

  it('rounds cents to whole dollars', () => {
    expect(formatCurrency(374.6)).toBe('$375')
  })
})

describe('formatTimeSince', () => {
  const now = new Date('2026-11-23T12:00:00Z')
  const minutesAgo = (minutes: number) => new Date(now.getTime() - minutes * 60_000)

  it.each([
    [0, 'just now'],
    [0.5, 'just now'],
    [1, '1 min ago'],
    [59, '59 min ago'],
    [60, '1 hour ago'],
    [150, '2 hours ago'],
    [24 * 60, '1 day ago'],
    [3 * 24 * 60, '3 days ago'],
  ])('%s minutes ago reads "%s"', (minutes, expected) => {
    expect(formatTimeSince(minutesAgo(minutes), now)).toBe(expected)
  })

  it('treats a time slightly in the future as just now', () => {
    expect(formatTimeSince(new Date(now.getTime() + 30_000), now)).toBe('just now')
  })
})
