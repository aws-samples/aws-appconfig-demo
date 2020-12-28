# Copyright Amazon.com, Inc. or its affiliates. All Rights Reserved.
# SPDX-License-Identifier: MIT-0

FROM trafex/alpine-nginx-php7:1.8.0

RUN curl https://docs.aws.amazon.com/aws-sdk-php/v3/download/aws.phar -o /var/www/html/aws.phar

COPY . /var/www/html/
