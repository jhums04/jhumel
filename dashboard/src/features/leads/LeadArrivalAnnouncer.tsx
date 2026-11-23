// Always rendered, so screen readers are already watching the region when a message is added.
export function LeadArrivalAnnouncer({ message }: { message: string }) {
  return (
    <p role="status" aria-live="polite" className="sr-only">
      {message}
    </p>
  )
}
