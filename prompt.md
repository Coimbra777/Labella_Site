Revise criticamente a nova regra de estoque do backend Laravel em /labella_painel.

Contexto:
O endpoint público POST /api/v1/orders agora cria apenas uma solicitação de pedido/orçamento.
A criação pública não baixa estoque.
O estoque deve baixar apenas quando o admin muda o pedido para processing.
O estoque deve voltar quando um pedido já confirmado/processado é cancelado.

Analise especialmente:

- app/Models/Order.php
- app/Http/Controllers/Admin/OrderController.php
- app/Filament/Resources/OrderResource/Pages/EditOrder.php
- app/Filament/Resources/OrderResource.php
- tests/Feature/Security/OrderCheckoutSecurityTest.php

Quero garantir que não exista:

1. baixa de estoque duplicada
2. devolução de estoque duplicada
3. estoque voltando em pedido que nunca foi confirmado
4. pedido indo para processing sem estoque suficiente
5. pedido cancelado voltando para processing sem nova validação
6. edição de itens após processing causando inconsistência
7. soft delete causando inconsistência
8. mudança de status pelo Filament diferente da mudança pela API admin

Analise todas as transições possíveis de status:

- pending → processing
- pending → cancelled
- processing → cancelled
- processing → shipped
- shipped → delivered
- shipped → cancelled
- delivered → cancelled
- cancelled → processing
- processing → pending
- cancelled → pending

Antes de alterar qualquer arquivo, entregue um relatório com:

1. Quais transições são seguras
2. Quais transições são perigosas
3. Quais regras deveriam existir
4. Onde a lógica está implementada hoje
5. Se a lógica está duplicada entre Model, Controller e Filament
6. Plano de correção

Depois, se necessário, implemente uma solução centralizada.

Preferência:

- Evite espalhar regra de estoque em vários lugares.
- Centralize a regra em um service, por exemplo OrderStatusService ou OrderStockService.
- Garanta transação no banco.
- Use lockForUpdate nos produtos ao baixar estoque.
- Crie testes para as transições críticas.

Crie testes para:

1. pending → processing baixa estoque uma vez
2. pending → cancelled não mexe no estoque
3. processing → cancelled devolve estoque uma vez
4. processing → processing não mexe novamente
5. cancelled → processing valida estoque e baixa corretamente
6. processing → pending, se permitido, deve devolver estoque ou ser bloqueado
7. delivered → cancelled deve ter comportamento definido
8. pedido sem estoque suficiente não pode ir para processing
