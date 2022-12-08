publish:
	docker buildx build --push --platform linux/arm/v7,linux/arm64/v8,linux/amd64 --tag matthiasmullie/post-to-email .

test:
	docker build -t post-to-email .
	docker run --rm -d \
		--name=post-to-email \
		-p 8080:80 \
		-e DSN="smtp://user:pass@smtp.example.com:587" \
		-e RECIPIENT="Matthias Mullie <post-to-email@mullie.eu>" \
		post-to-email
	sleep 3
	RESPONSE="$$(curl -s http://localhost:8080/?SENDER=test@example.com)";\
	docker stop post-to-email;\
	docker rmi post-to-email;\
	test "$$RESPONSE" = "OK"
