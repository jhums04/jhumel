import { useMutation, useQueryClient } from '@tanstack/react-query'
import { Outlet } from 'react-router'
import { useSignedInSession } from '../features/auth/useSession'
import { OrganizationSwitcher } from '../features/organizations/OrganizationSwitcher'
import { supabase } from '../lib/supabase'
import { Button } from './Button'

export function AppLayout() {
  const session = useSignedInSession()
  const queryClient = useQueryClient()

  const signOut = useMutation({
    mutationFn: async () => {
      const { error } = await supabase.auth.signOut()
      if (error) {
        throw error
      }
    },
    // Cached leads belong to the user who just left; the next user must not see them.
    onSuccess: () => {
      queryClient.clear()
    },
  })

  return (
    <div className="min-h-screen">
      <header className="border-b border-gray-200 bg-paper">
        <div className="mx-auto flex max-w-7xl items-center justify-between gap-3 px-4 py-3 sm:gap-4">
          <span className="font-serif text-lg font-semibold whitespace-nowrap">Lead Engine</span>
          <div className="flex items-center justify-end gap-3 sm:gap-4">
            <OrganizationSwitcher />
            <span className="hidden text-sm text-gray-600 sm:inline">{session.user.email}</span>
            <Button variant="secondary" onClick={() => signOut.mutate()} disabled={signOut.isPending}>
              Sign out
            </Button>
          </div>
        </div>
      </header>

      <main className="mx-auto max-w-7xl px-4 py-8">
        <Outlet context={session} />
      </main>
    </div>
  )
}
