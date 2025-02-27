<?php

return [
	'methods' => [
		'next' => 'Próximo',
		'title' => 'Por favor, escolha o método de pagamento',
		'empty' => 'Ainda não tenho um método de pagamento. Tente mais tarde.'
		],
	'credit' => [
		'no-point' => 'Você tem 0 pontos',
		],
	'CreditPaymentMethod' => [
		'guidelines' => 'Você pode pagar com crédito em sua conta da web. O crédito pode ser obtido de várias maneiras. Para mais detalhes, clique no link abaixo.',
		'url' => env('APP_URL') . '/blog/pay-with-credit-guidelines',
		],
	'TsrPaymentMethod' => [
		'guidelines' => 'Você pode pagar com cartão vietnamita pré-pago (cartões de jogo e telecomunicações). Atualmente, oferecemos suporte aos cartões Zing, Vinaphone e VTC. Para mais detalhes, clique no link abaixo',
		'url' => env('APP_URL') . '/blog/pay-with-tsr-guidelines',
		],
	'PaytrPaymentMethod' => [
		'guidelines' => 'Você pode pagar com VISA/Master Card via paytr.com',
		'url' => env('APP_URL') . '/blog/pay-with-paytr-guidelines',
		],
	'PaypalPaymentMethod' => [
		'guidelines' => 'Você pode pagar com sua conta bancária',
		'url' => env('APP_URL') . '/blog/pay-with-paypal-guidelines',
		],
	'MercadoPaymentMethod' => [
		'guidelines' => 'Você pode pagar via Mercado Pago',
		'url' => env('APP_URL') . '/blog/pay-with-mercado-guidelines',
		]
];