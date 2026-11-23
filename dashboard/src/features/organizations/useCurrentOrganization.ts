import { useQuery } from '@tanstack/react-query'
import { useSearchParams } from 'react-router'
import { chooseOrganization } from './chooseOrganization'
import { fetchOrganizations } from './organizationsApi'

// The choice lives in ?organization= so it survives a refresh and can be bookmarked.
export function useCurrentOrganization() {
  const organizations = useQuery({ queryKey: ['organizations'], queryFn: fetchOrganizations })
  const [searchParams, setSearchParams] = useSearchParams()

  const currentOrganization =
    organizations.data === undefined
      ? undefined
      : chooseOrganization(organizations.data, searchParams.get('organization'))

  function selectOrganization(slug: string) {
    setSearchParams({ organization: slug })
  }

  return { organizations, currentOrganization, selectOrganization }
}
