import { useCurrentOrganization } from './useCurrentOrganization'

// Shown only to users in more than one organization, such as the owner who also runs the demo.
export function OrganizationSwitcher() {
  const { organizations, currentOrganization, selectOrganization } = useCurrentOrganization()

  if (organizations.data === undefined || organizations.data.length < 2 || currentOrganization === undefined) {
    return null
  }

  return (
    <div className="flex items-center gap-2">
      {/* On phones the label is read by screen readers only, so the header fits on one line. */}
      <label htmlFor="organization-switcher" className="sr-only text-sm text-gray-600 sm:not-sr-only">
        Organization
      </label>
      <select
        id="organization-switcher"
        value={currentOrganization.slug}
        onChange={(event) => selectOrganization(event.target.value)}
        // 16px text on phones: iOS Safari zooms the page into any field with smaller text. 44px tall for a finger.
        className="min-h-11 rounded-lg border border-gray-200 bg-white px-2 text-base sm:text-sm"
      >
        {organizations.data.map((organization) => (
          <option key={organization.id} value={organization.slug}>
            {organization.name}
          </option>
        ))}
      </select>
    </div>
  )
}
