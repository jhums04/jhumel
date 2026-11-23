import { useEffect, useState } from 'react'

// Re-renders once a minute so relative times like "5 min ago" stay accurate.
export function useCurrentTime(): Date {
  const [currentTime, setCurrentTime] = useState(() => new Date())

  useEffect(() => {
    const intervalId = window.setInterval(() => setCurrentTime(new Date()), 60_000)
    return () => window.clearInterval(intervalId)
  }, [])

  return currentTime
}
