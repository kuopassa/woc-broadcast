<?php

# Broadcasts a single transaction to BSV blockchain using WhatsOnChain API.
# Usage example: https://localhost/woc-broadcast/?network=mainnet

declare(strict_types=1);

const
	WOC_API_URL = 'https://api.whatsonchain.com/v1/bsv/%s/tx/raw',
	WOC_NETWORKS = array(
		'mainnet'=>'main',
		'testnet'=>'test',
	);
	
function prepare(string $transaction):string {
	
	$transaction = array(
		'txhex'=>$transaction,
	);

	return (string) json_encode($transaction);
}
	
function broadcast(string $url,string $transaction):string {
	
	$options = array(
		'http'=>array(
			'method'=>'POST',
			'header'=>'Content-Type: application/json',
			'content'=>$transaction,
		),
	);
	
	$context = stream_context_create($options);

	return (string) file_get_contents(
		$url,
		false,
		$context
	);
}

if (
	(isset($_GET['network']))
	&&
	(array_key_exists(
		$_GET['network'],
		WOC_NETWORKS)
	)
) {
	
	# Signed transaction in hex:
	$hex = '01000000019257bc986c39d1714ce35113bfff4b159f782540cbccaf1c72a9244982d133a0010000006a47304402205c3257a4828be37e8f329f5a5ce97a7ad36307055876b63c21c27599fb53f75f022021b84dc6a76e3e3d4d05868dd59debb51b8aea891f67e9b7b95eb721b6c50197412103ebb20053a4ff8045b2bd9454d6c3447224c5bb40739e0b297ab889a8745d6df7ffffffff02e8030000000000001976a914ba278afec4e090995fd60a072e5ba4806525ceab88ac0d660000000000001976a91416d6fb5058ac1df2163c2a51d8fd6f70a266bc5288ac00000000';
	
	$url = sprintf(
		WOC_API_URL,
		WOC_NETWORKS[$_GET['network']]
	);
	
	$transaction = prepare($hex);
	
	$broadcast = broadcast(
		$url,
		$transaction
	);
	
	$broadcast = trim($broadcast);
	$broadcast = trim($broadcast,'"');
	
	# Returns transaction identifier if successful:
	var_dump($broadcast);
}