// Mirrors the quote form plugin: priced services from its settings (Settings.php) and the
// project brief's choices (ProjectBrief.php).
const serviceLabels: Partial<Record<string, string>> = {
  systems_crm: 'Systems and CRMs',
  automation_ai: 'Automations and AI',
  integrations: 'Integrations',
  websites: 'Websites and web apps',
  dashboards: 'Dashboards and reporting',
  solution_design: 'Solution design',
  other: 'Something else',
  automation: 'n8n automation workflow',
  lead_system: 'Full lead-to-CRM system',
  supabase_app: 'React + Supabase web app',
  wordpress_plugin: 'Custom WordPress plugin',
  rescue: 'App rescue or security audit',
}

const timelineLabels: Partial<Record<string, string>> = {
  standard: 'Standard, no rush',
  rush: 'Rush, as soon as possible',
}

// Mirrors ProjectBrief::budgets() in the plugin and the leads.budget check constraint.
const budgetLabels: Partial<Record<string, string>> = {
  under_1k: 'Under $1k',
  '1k_to_3k': '$1k to $3k',
  '3k_to_10k': '$3k to $10k',
  over_10k: '$10k+',
  not_sure: 'Not sure yet',
}

// Unknown keys are shown as they are, so a service added in WordPress still appears.
export function getServiceLabel(serviceKey: string): string {
  return serviceLabels[serviceKey] ?? serviceKey
}

export function getTimelineLabel(timeline: string): string {
  return timelineLabels[timeline] ?? timeline
}

export function getBudgetLabel(budget: string): string {
  return budgetLabels[budget] ?? budget
}
