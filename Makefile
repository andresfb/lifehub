ANSIBLE_DIR := packages/ansible

.PHONY: deploy-api-web deploy-api-worker deploy-front-web deploy-front-worker deploy-all

deploy-api-web:
	cd $(ANSIBLE_DIR) && ansible-playbook deploy_api_web.yml

deploy-api-worker:
	cd $(ANSIBLE_DIR) && ansible-playbook deploy_api_worker.yml --ask-become-pass

deploy-front-web:
	cd $(ANSIBLE_DIR) && ansible-playbook deploy_front_web.yml

deploy-front-worker:
	cd $(ANSIBLE_DIR) && ansible-playbook deploy_front_worker.yml --ask-become-pass

deploy-all: deploy-api-web deploy-api-worker deploy-front-web deploy-front-worker
