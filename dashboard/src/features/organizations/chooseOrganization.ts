import type { Organization } from './organizationsApi'

// The organization named in the URL if the user belongs to it, otherwise their real one before the demo.
export function chooseOrganization(
  organizations: Organization[],
  requestedSlug: string | null,
): Organization | undefined {
  return (
    organizations.find((organization) => organization.slug === requestedSlug) ??
    organizations.find((organization) => !organization.is_demo) ??
    organizations[0]
  )
}
