import { QueryClient, QueryClientProvider } from '@tanstack/react-query'
import { act, renderHook, waitFor } from '@testing-library/react'
import type { ReactNode } from 'react'
import { beforeEach, describe, expect, it, vi } from 'vitest'
import { createTestLead } from '../../test/createTestLead'
import { updateLeadStage, type Lead } from './leadsApi'
import { leadsQueryKey } from './useLeads'
import { useUpdateLeadStage } from './useUpdateLeadStage'

vi.mock('./leadsApi', () => ({
  fetchLeads: vi.fn<() => Promise<Lead[]>>(),
  updateLeadStage: vi.fn<() => Promise<void>>(),
}))

const organizationId = 'organization-1'
const lead = createTestLead()
let queryClient: QueryClient

beforeEach(() => {
  queryClient = new QueryClient({ defaultOptions: { queries: { retry: false, staleTime: Infinity } } })
  queryClient.setQueryData(leadsQueryKey(organizationId), [lead])
})

function cachedStage(): string | undefined {
  return queryClient.getQueryData<Lead[]>(leadsQueryKey(organizationId))?.[0]?.stage
}

function renderUpdateLeadStage() {
  const wrapper = ({ children }: { children: ReactNode }) => (
    <QueryClientProvider client={queryClient}>{children}</QueryClientProvider>
  )
  return renderHook(() => useUpdateLeadStage(organizationId), { wrapper })
}

describe('useUpdateLeadStage', () => {
  it('moves the lead before Supabase answers', async () => {
    let finishUpdate: () => void = () => undefined
    vi.mocked(updateLeadStage).mockReturnValue(new Promise<void>((resolve) => (finishUpdate = resolve)))
    const { result } = renderUpdateLeadStage()

    act(() => result.current.mutate({ lead, stage: 'contacted' }))

    await waitFor(() => expect(cachedStage()).toBe('contacted'))
    expect(result.current.isPending).toBe(true)
    act(() => finishUpdate())
  })

  it('moves the lead back when Supabase rejects the change', async () => {
    vi.mocked(updateLeadStage).mockRejectedValue(new Error('permission denied'))
    const { result } = renderUpdateLeadStage()

    act(() => result.current.mutate({ lead, stage: 'contacted' }))

    await waitFor(() => expect(result.current.isError).toBe(true))
    expect(cachedStage()).toBe('new')
  })
})
