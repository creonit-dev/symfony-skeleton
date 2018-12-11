FROM node:10.13

RUN npm install -g gulp bower
RUN echo '{ "allow_root": true }' > /root/.bowerrc

RUN mkdir /.npm; mkdir /.config; mkdir /.cache; mkdir /.local; chmod 777 /.npm; chmod 777 /.config; chmod 777 /.cache; chmod 777 /.local;

EXPOSE 4000

WORKDIR /app/markup