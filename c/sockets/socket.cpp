#include <stdio.h>
#include <sys/socket.h>
#include <arpa/inet.h>
#include <string.h>
#include <unistd.h>
#include <iostream>
#include <sys/sendfile.h>
#include <sys/types.h>
#include <sys/stat.h>
#include <errno.h>

#include "socket.h" 
#include "socket_exception.h"

using namespace std;

/*
 * Default constructor
 */
Socket::Socket() : m_sock(-1) {

#ifdef __DEBUG
	cout << "Socket::Socket()" << endl;
#endif

	memset(&m_addr, 0, sizeof(m_addr));
	m_addr.sin_family = AF_INET; /* Internet Address family */

	if ( (m_sock = socket(PF_INET, SOCK_STREAM, IPPROTO_TCP)) < 0 )
		throw SocketException("Failed to create socket");
}

Socket::Socket(int socket_id) : m_sock(socket_id) {

#ifdef __DEBUG
	cout << "Socket::Socket(int)" << endl;
#endif

}


/*
 * Class Destructor
 * 
 * close the socket descriptor
 */
Socket::~Socket() {

#ifdef __DEBUG
	cout << "Socket::~Socket()" << endl;
#endif

	close();
}


/*
 */
void Socket::close() {

#ifdef __DEBUG
	cout << "Socket::close()" << endl;
#endif

	::close(m_sock);
	m_sock = -1;
}


/*
 * Send the packet header (total is currently 8 bytes)
 */
inline int Socket::write_header(packet *message) {

	int sent_bytes;

#ifdef __DEBUG
	cout << "Socket::write_header(packet *)" << endl;
	cout << "Sending " << sizeof(packet) << " total bytes" << endl;
#endif

	if ((sent_bytes = ::send(m_sock, message, sizeof(packet), 0)) < 0)
		throw SocketException("Unable to transfer packet header");
	
#ifdef __DEBUG
	cout << "Sent " << sent_bytes << " bytes" << endl;
#endif

	return sent_bytes;
}


/*
 * Send the payload
 */
inline int Socket::write_data(char *data, size_t d_size) {

	int sent_bytes;
#ifdef __DEBUG
	cout << "Socket::write(char *, size_t)" << endl;
	cout << "Sending " << d_size << " total bytes" << endl;
#endif

	if ((sent_bytes = ::send(m_sock, data, d_size, 0)) < 0)
		throw SocketException("Failed to transfer data");

#ifdef __DEBUG
	cout << "Sent " << sent_bytes << " bytes" << endl;
#endif
	
	return sent_bytes;
}

inline int Socket::write_file(int file_d, size_t d_size) {

	int sent_bytes;
#ifdef __DEBUG
	cout << "Socket::write(int)" << endl;
#endif
	if ( (sent_bytes = ::sendfile(m_sock, file_d, 0, d_size)) < 0)
		throw SocketException("Failed to transfer file");

	return sent_bytes;
}

/*
 */
inline int Socket::read_header(packet *message) {

	int read_bytes, receivedBytes;
	size_t bytes_left;

#ifdef __DEBUG
	cout << "Socket::read_header(packet *)" << endl;
	cout << "Reading " << sizeof(packet) << " total bytes" << endl;
#endif

	read_bytes = 0;
	bytes_left = sizeof(packet);
	while (read_bytes < sizeof(packet)) {
		if( (receivedBytes = 
					::recv(m_sock, message + read_bytes, bytes_left, 0)) < 0)
			throw SocketException("Error reading from socket");

		read_bytes += receivedBytes;
		bytes_left -= receivedBytes;
#ifdef __DEBUG
		cout << "::recv returned " << receivedBytes << " bytes" << endl;
#endif
	}
	
#ifdef __DEBUG
	cout << "Read " << read_bytes << " bytes" << endl;
#endif

	return read_bytes;
}


/*
 */
inline int Socket::read_data(char *data, size_t d_size) {

	int read_bytes, receivedBytes;
	read_bytes = 0;

#ifdef __DEBUG
	cout << "Socket::read_data(char *, size_t)" << endl;
	cout << "Reading " << d_size << " total bytes" << endl;
#endif

	while (read_bytes < d_size) {
		receivedBytes = ::recv(m_sock, data + read_bytes, d_size, 0);
		read_bytes += receivedBytes;
		d_size -= receivedBytes;
		cout << "::recv returned " << receivedBytes << " bytes" << endl;
	}

	/* add null terminator */
	data[read_bytes] = '\0';

#ifdef __DEBUG
	cout << "Read " << read_bytes << " bytes" << endl;
#endif

	return read_bytes;
}


/*
 */
inline int Socket::read_file(int file_d, size_t d_size) {

	int read_bytes, receivedBytes;
	size_t read_size;
	char buffer[1024];

	read_size = d_size < 1024 ? d_size : 1024;
	read_bytes = 0;

#ifdef __DEBUG
	cout << "Socket::read_file(int, size_t)" << endl;
	cout << "Reading " << d_size << " total bytes" << endl;
#endif

	while (read_bytes < d_size) {
		receivedBytes = ::recv(m_sock, buffer + read_bytes, read_size, 0);
		read_bytes += receivedBytes;

		if ( (d_size - read_bytes) < 1024)
			read_size = d_size - read_bytes;

		// write contents to file descriptor
		::write(file_d, buffer, receivedBytes);
	}

#ifdef __DEBUG
	cout << "Read " << read_bytes << " bytes" << endl;
#endif

	return read_bytes;
}

// implement << operator for pointer data
void Socket::write(char *data) {

	packet message;
#ifdef __DEBUG
	cout << "Socket::write(char *)" << endl;
#endif

	/* create packet header */
	message.d_type = m_data;
	message.d_size = strlen(data);
	
	write_header(&message);
	write_data(data, message.d_size);
}

// implement << operator for integer data
void Socket::write(int file_d) {

	packet message;
	struct stat sbuf;

#ifdef __DEBUG
	cout << "Socket::operator<<(int)" << endl;
#endif

	/* create message header */
	message.d_type = f_data;

	/* determine size of the file (in bytes) */
	fstat(file_d, &sbuf);
	message.d_size = sbuf.st_size; /* figure out size of the file */

	write_header(&message);
	write_file(file_d, message.d_size);
}


char *Socket::read() {

	packet message;
	char *data;

#ifdef __DEBUG
	cout << "Socket::read(char *)" << endl;
#endif

	read_header(&message);
	data = (char *)malloc(message.d_size + 1);
   read_data(data, message.d_size);

	return data;
}

void Socket::read(int file_d) {

	packet message;

#ifdef __DEBUG
	cout << "Socket::read()" << endl;
#endif

	read_header(&message);
	read_file(file_d, message.d_size);
}
