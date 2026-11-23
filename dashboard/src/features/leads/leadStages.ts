import { Constants, type Database } from '../../types/database'

export type LeadStage = Database['public']['Enums']['lead_stage']

const stageLabels: Record<LeadStage, string> = {
  new: 'New',
  contacted: 'Contacted',
  proposal_sent: 'Proposal sent',
  won: 'Won',
  lost: 'Lost',
}

// Board column order, taken from the database enum so the two cannot drift apart.
export const leadStages: readonly LeadStage[] = Constants.public.Enums.lead_stage

export function getStageLabel(stage: LeadStage): string {
  return stageLabels[stage]
}

export function isLeadStage(value: unknown): value is LeadStage {
  return typeof value === 'string' && value in stageLabels
}
