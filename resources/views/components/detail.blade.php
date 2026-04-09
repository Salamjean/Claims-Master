@props(['label', 'value' => null])
<div>
    <p class="label">{{ $label }}</p>
    @if($value)
        <p class="detail-value">{{ $value }}</p>
    @else
        <p class="detail-empty">Non renseigné</p>
    @endif
</div>

<style>
    .label {
        display: block;
        font-size: 10.5px;
        font-weight: 700;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: .05em;
        margin-bottom: 2px;
    }

    .detail-value {
        font-size: 13.5px;
        color: #1e293b;
        font-weight: 500;
        white-space: pre-wrap;
        line-height: 1.5;
    }

    .detail-empty {
        font-size: 13px;
        color: #cbd5e1;
        font-style: italic;
    }
</style>