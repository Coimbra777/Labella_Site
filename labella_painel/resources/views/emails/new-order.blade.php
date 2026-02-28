<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        h1 { color: #e91e63; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        th, td { padding: 8px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f5f5f5; }
        .btn { display: inline-block; padding: 12px 24px; background: #e91e63; color: white !important; text-decoration: none; border-radius: 6px; margin-top: 15px; }
        .btn:hover { background: #c2185b; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Novo pedido recebido!</h1>
        <p>O pedido <strong>#{{ $order->order_number }}</strong> foi realizado.</p>

        <h2>Cliente</h2>
        <table>
            <tr><th>Nome</th><td>{{ $order->customer_name }}</td></tr>
            @if($order->customer_email)
            <tr><th>E-mail</th><td>{{ $order->customer_email }}</td></tr>
            @endif
            @if($order->customer_phone)
            <tr><th>Telefone</th><td>{{ $order->customer_phone }}</td></tr>
            @endif
            <tr><th>Cidade</th><td>{{ $order->shipping_city }}</td></tr>
            @if($order->payment_method)
            <tr><th>Pagamento</th><td>{{ $order->payment_method }}</td></tr>
            @endif
        </table>

        <h2>Itens</h2>
        <table>
            <thead>
                <tr><th>Produto</th><th>Qtd</th><th>Preço</th><th>Subtotal</th></tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                <tr>
                    <td>{{ $item->product_name }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>R$ {{ number_format($item->price, 2, ',', '.') }}</td>
                    <td>R$ {{ number_format($item->subtotal, 2, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <p><strong>Total: R$ {{ number_format($order->total, 2, ',', '.') }}</strong></p>

        @if($order->notes)
        <p><strong>Observações:</strong> {{ $order->notes }}</p>
        @endif

        <a href="{{ config('app.url') }}/admin/orders/{{ $order->id }}/edit" class="btn">Gerenciar pedido no painel</a>
    </div>
</body>
</html>
