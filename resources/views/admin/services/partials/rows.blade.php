@forelse($services as $svc)
<tr>
  @php
      // These 3 are still protected from deletion
      $protected = in_array($svc->title, [
          'Full Service',
          'Drop-Off Service',
          'Self-Service',
      ]);
  @endphp

  {{-- Title (LEFT) --}}
  <td class="text-start">{{ $svc->title }}</td>

  {{-- Description -> View modal (CENTER via CSS class) --}}
  <td class="desc-col">
    <button type="button"
            class="btn btn-outline-primary btn-sm"
            data-bs-toggle="modal"
            data-bs-target="#svcDescModal-{{ $svc->id }}">
      View
    </button>

    <!-- Description Modal -->
    <div class="modal fade" id="svcDescModal-{{ $svc->id }}" tabindex="-1"
         aria-labelledby="svcDescLabel-{{ $svc->id }}" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header" style="background:#173F7B;color:#fff;">
            <h5 class="modal-title" id="svcDescLabel-{{ $svc->id }}">
              {{ $svc->title }} — Description
            </h5>
            <button type="button"
                    class="btn-close btn-close-white"
                    data-bs-dismiss="modal"
                    aria-label="Close"></button>
          </div>
          <div class="modal-body">
            {!! nl2br(e($svc->description ?: '—')) !!}
          </div>
          <div class="modal-footer">
            <button type="button"
                    class="btn btn-secondary btn-sm"
                    data-bs-dismiss="modal">
              Close
            </button>
          </div>
        </div>
      </div>
    </div>
  </td>

  {{-- Price --}}
  <td class="text-center">₱ {{ number_format($svc->price, 0) }}</td>

  {{-- Active --}}
  <td class="text-center">
    @if($svc->is_active)
        <span class="badge bg-success">Yes</span>
    @else
        <span class="badge bg-light text-muted">No</span>
    @endif
  </td>

  {{-- Sort Order --}}
  <td class="text-center">{{ $svc->sort_order }}</td>

  {{-- Actions --}}
  <td class="actions text-center">
    <a href="{{ route('service.editForm', $svc->id) }}"
       class="btn btn-outline-primary btn-lg px-3 py-1"
       title="Edit service">
        <i class="bi bi-pencil-square"></i>
    </a>

    @unless ($protected)
        <form action="{{ route('service.delete', $svc->id) }}"
              method="GET"
              class="d-inline">
            <button type="submit"
                    class="btn btn-sm btn-outline-danger"
                    onclick="return confirm('Delete this service?');"
                    title="Delete service">
                <i class="bi bi-trash"></i>
            </button>
        </form>
    @endunless
  </td>
</tr>
@empty
<tr>
  <td colspan="6" class="text-center text-muted">No services yet.</td>
</tr>
@endforelse
