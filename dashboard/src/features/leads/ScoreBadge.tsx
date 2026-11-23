import { Badge } from '../../components/Badge'
import type { Database } from '../../types/database'

type LeadScore = Database['public']['Enums']['lead_score']

const scoreBadges = {
  high: { variant: 'solid', label: 'High' },
  medium: { variant: 'outline', label: 'Medium' },
  low: { variant: 'muted', label: 'Low' },
} as const

export function ScoreBadge({ score }: { score: LeadScore | null }) {
  if (score === null) {
    return <Badge variant="dashed">Unscored</Badge>
  }
  const scoreBadge = scoreBadges[score]
  return <Badge variant={scoreBadge.variant}>{scoreBadge.label}</Badge>
}
