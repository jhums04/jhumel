import { Navigate, Outlet, useLocation } from 'react-router'
import { useSession } from './useSession'

export function RequireAuth() {
  const sessionState = useSession()
  const location = useLocation()

  if (sessionState.status === 'loading') {
    return (
      <p role="status" className="px-4 py-16 text-center text-gray-600">
        Loading...
      </p>
    )
  }

  if (sessionState.status === 'signed-out') {
    return <Navigate to="/login" replace state={{ from: `${location.pathname}${location.search}` }} />
  }

  return <Outlet context={sessionState.session} />
}
