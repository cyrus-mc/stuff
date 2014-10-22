all : server_test client_test

server_test : server_test.o socket.o server_socket.o
	g++ -o server_test server_test.o socket.o server_socket.o

client_test : client_test.o socket.o client_socket.o
	g++ -o client_test client_test.o socket.o client_socket.o

server_test.o : server_test.cpp
	g++ -c -D__DEBUG server_test.cpp

client_test.o : client_test.cpp
	g++ -c -D__DEBUG client_test.cpp

socket.o : socket.cpp socket.h
	g++ -c -D__DEBUG socket.cpp

server_socket.o : server_socket.cpp server_socket.h
	g++ -c -D__DEBUG server_socket.cpp

client_socket.o : client_socket.cpp client_socket.h
	g++ -c -D__DEBUG client_socket.cpp

clean :
	rm -rf *.o server_test client_test
