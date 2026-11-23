import { useCurrentOrganization } from './useCurrentOrganization'

// Shown only to users in more than one organization, such as the owner who also runs the demo.
export function OrganizationSwitcher() {
  const { organizations, currentOrganization, selectOrganization } = useCurrentOrganization()

  if (organizations.data === undefined || organizations.data.length < 2 || currentOrganization === undefined) {
    return null
  }

  return (
    <div className="flex items-center gap-2">
      <label htmlFor="organization-switcher" className="text-sm text-gray-600">
        Organization
      </label>
      <select
        id="organization-switcher"
        value={currentOrganization.slug}
        onChange={(event) => selectOrganization(event.target.value)}
        className="rounded border border-gray-200 bg-white px-2 py-2 text-sm"
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
