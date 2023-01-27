publish:
	docker buildx build --push --platform linux/arm/v7,linux/arm64/v8,linux/amd64 --tag matthiasmullie/post-to-email .

test:
	docker build -t post-to-email .
	docker run --rm -d \
		--name=post-to-email-1 \
		-p 8081:80 \
		-e ALLOW_ORIGIN="*" \
		-e DSN="smtp://user:pass@smtp.example.com:587" \
		-e RECIPIENT="Matthias Mullie <post-to-email@mullie.eu>" \
		post-to-email
	docker run --rm -d \
		--name=post-to-email-2 \
		-p 8082:80 \
		-e DSN="smtp://user:pass@smtp.example.com:587" \
		-e RECIPIENT="Matthias Mullie <post-to-email@mullie.eu>" \
		post-to-email
	docker run --rm -d \
		--name=post-to-email-3 \
		-p 8083:80 \
		-e ALLOW_ORIGIN="*" \
		-e RECIPIENT="Matthias Mullie <post-to-email@mullie.eu>" \
		post-to-email
	docker run --rm -d \
		--name=post-to-email-4 \
		-p 8084:80 \
		-e ALLOW_ORIGIN="*" \
		-e DSN="smtp://user:pass@smtp.example.com:587" \
		-e RECIPIENT="Matthias Mullie <post-to-email@mullie.eu>" \
		-e HONEYPOT="honeypot" \
		post-to-email
	sleep 3
	RESPONSE_1="$$(curl -s http://localhost:8081/?SENDER=test@example.com)";\
	RESPONSE_2="$$(curl -s http://localhost:8082/?SENDER=test@example.com)";\
	RESPONSE_3="$$(curl -s http://localhost:8083/?SENDER=test@example.com)";\
	RESPONSE_4A="$$(curl -s http://localhost:8084/?SENDER=test@example.com)";\
	RESPONSE_4B="$$(curl -s -d honeypot=value http://localhost:8084/?SENDER=test@example.com)";\
	docker stop post-to-email-1;\
	docker stop post-to-email-2;\
	docker stop post-to-email-3;\
	docker stop post-to-email-4;\
	docker rmi post-to-email;\
	test "$$RESPONSE_1" = "OK" &&\
	test "$$RESPONSE_2" = "Missing config for 'ALLOW_ORIGIN'" &&\
	test "$$RESPONSE_3" = "Missing config for 'DSN'" &&\
	test "$$RESPONSE_4A" = "OK" &&\
	test "$$RESPONSE_4B" = "Spam detected"
